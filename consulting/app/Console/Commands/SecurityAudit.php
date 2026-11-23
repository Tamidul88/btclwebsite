<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Production-readiness security audit.
 *
 * This stands in for Enlightn, which cannot be installed here: its last
 * release (v2.10.0, April 2024) caps at Laravel 11 and pins larastan ^2.0,
 * while Laravel 12 requires larastan ^3.0. Rather than pin the whole project
 * to an abandoned package's constraints, the checks it is known for are
 * implemented directly — and unlike Enlightn, these know about this
 * application's own controls (admin path, 2FA enforcement, upload policy).
 *
 * Exit code 1 on any failure, so CI and the deploy script can gate on it.
 * `--production` additionally treats environment-specific warnings as
 * failures; without it they are advisory, so the command is still useful
 * locally.
 */
class SecurityAudit extends Command
{
    protected $signature = 'security:audit {--production : Treat production-only warnings as failures}';

    protected $description = 'Check the application against its production security baseline';

    /** @var list<array{status: string, area: string, detail: string}> */
    private array $results = [];

    public function handle(): int
    {
        $this->auditEnvironment();
        $this->auditSessionAndCookies();
        $this->auditAdminPanel();
        $this->auditUploads();
        $this->auditDatabase();
        $this->auditFilePermissions();
        $this->auditDependencies();

        return $this->report();
    }

    private function auditEnvironment(): void
    {
        $this->check(
            'environment',
            'APP_DEBUG is off',
            config('app.debug') === false,
            'Debug mode leaks the stack trace, environment and request payload on any error.',
            productionOnly: true,
        );

        $this->check(
            'environment',
            'APP_ENV is production',
            app()->isProduction(),
            'Non-production environments relax several framework safety defaults.',
            productionOnly: true,
        );

        $key = (string) config('app.key');

        $this->check(
            'environment',
            'APP_KEY is set',
            $key !== '',
            'Without APP_KEY nothing encrypted — including 2FA secrets — is recoverable or safe.',
        );

        $this->check(
            'environment',
            'APP_URL uses https',
            str_starts_with((string) config('app.url'), 'https://'),
            'Signed URLs and the sitemap are generated from APP_URL.',
            productionOnly: true,
        );

        $this->check(
            'environment',
            '.env is not world-readable',
            ! File::exists(base_path('.env'))
                || (fileperms(base_path('.env')) & 0o044) === 0,
            'The .env file holds the app key and database password; it should be 600.',
        );
    }

    private function auditSessionAndCookies(): void
    {
        $this->check('session', 'Cookies are marked Secure', (bool) config('session.secure'),
            'A cookie without Secure can be sent over plain http.', productionOnly: true);

        $this->check('session', 'Cookies are HttpOnly', (bool) config('session.http_only'),
            'Without HttpOnly, any XSS can read the session cookie.');

        $this->check('session', 'SameSite is strict or lax',
            in_array(config('session.same_site'), ['strict', 'lax'], true),
            'SameSite=none exposes the session to cross-site requests.');

        $this->check('session', 'Session payload is encrypted', (bool) config('session.encrypt'),
            'Unencrypted session data is readable by anyone with database or file access.');

        $this->check('session', 'Session lifetime is 60 minutes or less',
            (int) config('session.lifetime') <= 60,
            'Long sessions widen the window for a stolen cookie.');
    }

    private function auditAdminPanel(): void
    {
        $path = (string) config('security.admin_path');

        $this->check('admin', 'Admin path is not a default',
            ! in_array($path, ['admin', 'administrator', 'backend', 'panel', 'dashboard'], true),
            'A default path puts the panel on every commodity scanner wordlist.');

        $this->check('admin', 'Admin path differs from the shipped example',
            $path !== 'ctrl-a9f2',
            'The value in .env.example is public; change it before deploying.',
            productionOnly: true);

        $this->check('admin', 'Two-factor authentication is enforced',
            (bool) config('security.two_factor.enforce'),
            'Password-only access to the panel is a single point of failure.');

        $this->check('admin', 'Login lockout is 5 attempts or fewer',
            (int) config('security.login.max_attempts') <= 5,
            'A high threshold makes credential stuffing practical.');

        /*
         * Everything below needs the database. It may legitimately be
         * unreachable — a CI job that never migrates, or a config-only check
         * before the server is provisioned — and an audit command that
         * throws in that situation is one nobody runs. Degrade to a reported
         * finding instead.
         */
        try {
            if (! Schema::hasTable('users')) {
                $this->results[] = [
                    'status' => 'warn',
                    'area' => 'admin',
                    'detail' => 'User table not migrated; account checks skipped.',
                ];

                return;
            }
        } catch (\Throwable $e) {
            $this->results[] = [
                'status' => 'warn',
                'area' => 'admin',
                'detail' => 'Database unreachable; account checks skipped ('.class_basename($e).').',
            ];

            return;
        }

        $without2fa = User::query()->whereNull('two_factor_confirmed_at')->count();

        $this->check('admin', 'Every user has confirmed 2FA', $without2fa === 0,
            "{$without2fa} user(s) have not completed 2FA enrolment.", productionOnly: true);

        $seeded = User::query()->where('email', 'admin@example.test')->exists();

        $this->check('admin', 'Development seed account is absent', ! $seeded,
            'admin@example.test is a published credential; it must never exist in production.',
            productionOnly: true);

        // Production-only: a fresh install or a CI database legitimately has
        // no admin yet — `admin:create` is the next step. It is only a
        // failure once the site is live.
        $this->check('admin', 'At least one active admin exists',
            User::query()->where('is_active', true)->count() > 0,
            'Nobody can administer the site.',
            productionOnly: true);
    }

    private function auditUploads(): void
    {
        $mimes = (array) config('security.uploads.mimes');
        $extensions = (array) config('security.uploads.extensions');

        $dangerous = ['php', 'phtml', 'php5', 'phar', 'svg', 'html', 'htm', 'js', 'sh', 'exe'];
        $overlap = array_intersect($extensions, $dangerous);

        $this->check('uploads', 'No executable or scriptable extensions allowed',
            $overlap === [],
            'Allowed: '.implode(', ', $overlap).' — these can execute or carry script.');

        $this->check('uploads', 'Upload size is capped',
            (int) config('security.uploads.max_kb') > 0
                && (int) config('security.uploads.max_kb') <= 20480,
            'An uncapped upload size is a cheap denial-of-service.');

        $this->check('uploads', 'Allowed MIME list is explicit',
            $mimes !== [] && ! in_array('*', $mimes, true),
            'Uploads must be restricted to a known set of content types.');

        $this->check('uploads', 'Attachment disk is outside the web root',
            ! str_starts_with((string) config('filesystems.disks.private.root'), public_path()),
            'Files reachable by URL can be served, and possibly executed.');
    }

    private function auditDatabase(): void
    {
        $driver = (string) config('database.default');
        $connection = (array) config("database.connections.{$driver}");

        $this->check('database', 'Database is not SQLite',
            $driver !== 'sqlite',
            'SQLite is fine for development but not for a production deployment here.',
            productionOnly: true);

        if ($driver === 'mysql') {
            $host = (string) ($connection['host'] ?? '');

            $this->check('database', 'MySQL host is loopback',
                in_array($host, ['127.0.0.1', 'localhost', '::1'], true),
                'The database should not be reachable over the network.',
                productionOnly: true);

            $this->check('database', 'Database password is set',
                (string) ($connection['password'] ?? '') !== '',
                'An empty database password is an open door.');

            $this->check('database', 'Application user is not root',
                ! in_array((string) ($connection['username'] ?? ''), ['root', 'admin'], true),
                'The app should connect with a least-privilege account.',
                productionOnly: true);
        }
    }

    private function auditFilePermissions(): void
    {
        foreach (['storage', 'bootstrap/cache'] as $writable) {
            $this->check('filesystem', "{$writable} is writable",
                is_writable(base_path($writable)),
                'The application cannot function without write access here.');
        }

        // Code must NOT be writable by the web server: that is what turns a
        // file-write bug into persistent remote code execution.
        $this->check('filesystem', 'Application code is not writable by others',
            (fileperms(app_path()) & 0o002) === 0,
            'World-writable code allows an attacker to persist.');

        $this->check('filesystem', 'No .env file inside public/',
            ! File::exists(public_path('.env')),
            'An env file under the document root may be served verbatim.');
    }

    private function auditDependencies(): void
    {
        $lock = base_path('composer.lock');

        $this->check('dependencies', 'composer.lock is committed',
            File::exists($lock),
            'Without a lockfile, deploys are not reproducible and audits are meaningless.');

        $this->check('dependencies', 'Dev dependencies are absent in production',
            ! app()->isProduction() || ! File::exists(base_path('vendor/pestphp')),
            'Install with --no-dev in production; test tooling widens the attack surface.',
            productionOnly: true);
    }

    private function check(
        string $area,
        string $detail,
        bool $passed,
        string $consequence = '',
        bool $productionOnly = false,
    ): void {
        if ($passed) {
            $this->results[] = ['status' => 'pass', 'area' => $area, 'detail' => $detail];

            return;
        }

        // A production-only check is advisory unless --production is passed,
        // so running this locally does not drown the real failures in noise.
        $status = ($productionOnly && ! $this->option('production')) ? 'warn' : 'fail';

        $this->results[] = [
            'status' => $status,
            'area' => $area,
            'detail' => $detail.($consequence !== '' ? ' — '.$consequence : ''),
        ];
    }

    private function report(): int
    {
        $failed = array_filter($this->results, fn ($r) => $r['status'] === 'fail');
        $warned = array_filter($this->results, fn ($r) => $r['status'] === 'warn');
        $passed = array_filter($this->results, fn ($r) => $r['status'] === 'pass');

        $this->newLine();

        foreach (['fail' => 'error', 'warn' => 'warn'] as $status => $method) {
            foreach (array_filter($this->results, fn ($r) => $r['status'] === $status) as $result) {
                $this->{$method}(sprintf('[%s] %s', strtoupper($result['area']), $result['detail']));
            }
        }

        if ($this->output->isVerbose()) {
            foreach ($passed as $result) {
                $this->line(sprintf('  <fg=green>✓</> [%s] %s', $result['area'], $result['detail']));
            }
        }

        $this->newLine();
        $this->line(sprintf(
            '%d passed, %d warning(s), %d failure(s).',
            count($passed),
            count($warned),
            count($failed),
        ));

        if ($warned !== [] && ! $this->option('production')) {
            $this->comment('Warnings are production-only checks. Re-run with --production before deploying.');
        }

        return $failed === [] ? self::SUCCESS : self::FAILURE;
    }
}
