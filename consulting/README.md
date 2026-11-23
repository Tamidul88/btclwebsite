# {{COMPANY}} — corporate consulting site

Security-first Laravel application: public marketing site plus a hardened
Filament admin panel.

**Stack:** Laravel 12 · PHP 8.4 · MySQL 8 (SQLite for dev/CI) · Blade ·
Tailwind CSS v4 · Alpine (CSP build) · Filament v3 · Pest · Larastan

---

## Two deviations from the original brief

**Laravel 12, not 11.** Every Laravel 11 release is subject to three published
advisories with **no patched 11.x release** — most seriously `CVE-2026-48019`,
a CRLF injection in the default `email` validation rule (fixed only in 12.60.0
/ 13.10.0). Laravel 11 is outside its security-support window, so the fix was
never backported. Composer refuses to install it, and `composer audit` in CI
would fail on the first run. Since every form here validates email, shipping
11 would contradict the brief. Filament v3 is unaffected — it declares
`^10.45|^11.0|^12.0|^13.0`, so the admin stack is exactly as specified.

**PHP 8.4, not 8.3** — agreed during setup; 8.3 was not available and Laravel
12 supports 8.4 natively. CI still runs the suite against 8.3 **and** 8.4.

**Enlightn is not installed.** Its last release (v2.10.0, April 2024) caps at
Laravel 11 and pins `larastan ^2.0`, while Laravel 12 needs `^3.0` — it cannot
be installed without downgrading the whole project onto an abandoned package's
constraints. `php artisan security:audit` implements the production-readiness
checks it is known for, plus checks specific to this application (admin path,
2FA enforcement, upload policy, attachment disk location), and is covered by
19 tests.

---

## Quick start

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Local admin (development seed only): `admin@example.test` / `Local-Dev-Password-9!`
at `/ctrl-a9f2`. 2FA enrolment is forced on first login.

**Production:** never run the seeder. Create the first admin with:

```bash
php artisan admin:create
```

The password is prompted for, never passed as an argument — arguments land in
shell history and the process list.

---

## Architecture

Content is **config-driven**, not database-driven: marketing copy lives in
`config/site.php`, is versioned with the code, cacheable via `config:cache`,
and carries no admin CRUD surface to secure. The database holds only what is
genuinely dynamic — enquiries, users, and the audit trail.

```
app/
├── Console/Commands/     admin:create · enquiries:prune (retention)
├── Filament/
│   ├── Pages/            TwoFactorSetup · TwoFactorChallenge
│   └── Resources/        EnquiryResource (read-only) · AuditLogResource
├── Http/
│   ├── Controllers/      Page · Enquiry · Attachment · Seo
│   ├── Middleware/       SecurityHeaders · AdminIpAllowlist · RequireTwoFactor · CachePage
│   └── Requests/         StoreEnquiryRequest
├── Models/               User · Enquiry · AuditLog (append-only)
├── Policies/             EnquiryPolicy · UserPolicy
├── Rules/                SafeAttachment (content-based upload validation)
├── Services/             AttachmentStorage · AuditLogger · TwoFactorService
└── Support/              Cidr
config/
├── security.php          admin path, allowlist, CSP, HSTS, uploads, 2FA
└── site.php              all marketing copy
docs/
├── server-hardening.md   Ubuntu + Nginx + PHP-FPM + MySQL + Cloudflare + backups
├── pre-launch-checklist.md
└── incident-response.md
```

### Routes

| Method | Path | Notes |
| --- | --- | --- |
| GET | `/`, `/about`, `/services`, `/case-studies`, `/legal/{doc}` | page-cached 10 min |
| GET | `/services/{slug}`, `/case-studies/{slug}` | 404 on unknown slug |
| GET | `/contact`, `/request-consultation` | never cached |
| POST | `/enquiries/{contact\|consultation}` | throttled + honeypot |
| GET | `/enquiries/{enquiry}/attachment` | auth + policy + audited |
| GET | `/sitemap.xml`, `/robots.txt` | |
| — | `/{ADMIN_PATH}` | Filament panel |

---

## Security controls

| Area | Control |
| --- | --- |
| Input | FormRequests only; length caps; `email:filter`; allowlisted selects; control chars stripped from single-line fields |
| Output | `{{ }}` everywhere. The only two `{!! !!}` are a self-generated QR SVG and a `json_encode`d LD+JSON block |
| SQL | Eloquent only; `$fillable` on every model; server-controlled columns never mass-assigned |
| AuthZ | Policies per model. **No `Gate::before` admin bypass** — it would silently defeat "cannot delete self" and "cannot edit an enquiry". `Gate::after` is deny-only |
| CSRF | Enabled everywhere; tested against a subclass that disables the framework's test-mode bypass |
| Session | 30 min, encrypted, `Secure` + `HttpOnly` + `SameSite=Strict`; regenerated on 2FA pass |
| Headers | Nonce CSP (no `unsafe-*` on the public site), HSTS, XFO, nosniff, Referrer-Policy, Permissions-Policy, COOP/CORP |
| Rate limits | Forms 5/min + 30/day per IP; login 5 per 15 min on **two** keys (IP and account); 2FA 5/min |
| Admin | Non-default path, optional IP allowlist (404 not 403), TOTP 2FA enforced, 12-char + HIBP passwords, full audit trail |
| Uploads | Extension **and** content MIME; images re-encoded through GD; UUID filenames; stored off the web root; served `attachment` + `octet-stream` |
| Privacy | IP stored as a keyed HMAC, never plaintext; 24-month retention enforced by a scheduled command |

### Trade-offs worth knowing

**CSP is strict on the public site, relaxed for the panel.** Filament bundles
Alpine's standard build, which compiles expressions with `new Function()` and
needs `'unsafe-eval'`. Rather than weaken the whole site, that exception is
scoped to the admin path, which also sits behind auth, 2FA, an optional IP
allowlist and a 30-minute session. The public site uses `@alpinejs/csp`, so it
needs no exception — at the cost of declaring behaviour via `Alpine.data()`
rather than inline expressions.

**The IP allowlist is registered globally, not on the panel.** Laravel sorts
route middleware by its priority list; `Authenticate` is in that list and a
custom class is not, so attaching it to the panel let the login redirect fire
*before* the IP check. A network boundary has to be outermost.

**The admin path is noise reduction, not a control.** It keeps the site off
commodity scanner wordlists and cuts log volume. The real controls are 2FA,
the allowlist and the rate limits.

**The audit log is append-only at the model layer** — `updating` and `deleting`
return `false`. The scheduled retention prune uses a mass delete, which
bypasses model events by design. If you suspect tampering, compare against an
off-server backup rather than the live table.

**Password breach checking is production-only.** `Password::uncompromised()`
calls the HIBP range API; enabling it in tests would make the suite depend on
an outbound request. If HIBP is unreachable, Laravel treats the password as
uncompromised — an outage degrades gracefully rather than locking everyone out.

---

## Testing

```bash
./vendor/bin/pest              # 171 tests, 413 assertions
./vendor/bin/phpstan analyse   # Larastan level 8, clean
./vendor/bin/pint --test
composer audit --locked
```

Coverage by area: security headers & CSP (8) · XSS and header injection (13) ·
CSRF (5) · cookies (6) · upload abuse (14) · authorization (13) · rate limits
& honeypot (9) · validation & mass assignment (15) · 2FA (17) · IP allowlist
(18) · auth logging (10) · security-audit command (19) · public site & SEO (22).

```bash
php artisan security:audit                # local: production checks advisory
php artisan security:audit --production   # deploy gate: exits 1 on any failure
```

**Two notes on test honesty.** Laravel's `ValidateCsrfToken` bypasses itself
under `runningUnitTests()`, so a CSRF test written against it asserts nothing —
`tests/Support/EnforcedCsrfToken.php` disables that bypass. And the reflected-XSS
tests use `->from('/contact')`: without it the failed POST redirects to `/`,
a page with no form, and every "does not contain payload" assertion passes
vacuously. Both are guarded explicitly.

---

## CI

`.github/workflows/ci.yml` runs on push, PR and weekly:

- `composer audit --locked` and `npm audit` (weekly too — advisories appear
  without any commit of ours)
- Larastan level 8
- Pint
- Pest across PHP 8.3/8.4 × SQLite/MySQL 8
- `security:audit --production` (replaces Enlightn — see below)
- OWASP ZAP baseline, weekly and on PRs
- Gitleaks secret scan

Dependabot covers Composer, npm and Actions.

---

## Operations

- [`docs/server-hardening.md`](docs/server-hardening.md) — full Ubuntu build:
  SSH, UFW, Fail2ban, Nginx, PHP-FPM pool with `open_basedir`, MySQL least
  privilege, Let's Encrypt, Cloudflare WAF, encrypted restic backups **with a
  tested restore procedure**
- [`docs/pre-launch-checklist.md`](docs/pre-launch-checklist.md)
- [`docs/incident-response.md`](docs/incident-response.md)
- [`docs/monitoring.md`](docs/monitoring.md) — what is logged, Sentry, uptime,
  alert thresholds, and how to verify the pipeline actually fires
- [`docs/dependency-policy.md`](docs/dependency-policy.md) — CVE patch windows
  (critical 24h / high 72h), Dependabot policy, advisory log
- [`docs/zap-scan.md`](docs/zap-scan.md) — running OWASP ZAP yourself

Before launch, replace the placeholder:

```bash
grep -rn '{{COMPANY}}' --include='*.php' --include='*.blade.php' .
```
