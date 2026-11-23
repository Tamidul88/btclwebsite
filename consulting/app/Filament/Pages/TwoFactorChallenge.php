<?php

namespace App\Filament\Pages;

use App\Http\Middleware\RequireTwoFactor;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\TwoFactorService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Per-session TOTP challenge.
 *
 * Rate limited independently of login: an attacker with a valid password must
 * still brute-force 10^6 codes, and gets five attempts a minute to do it.
 */
class TwoFactorChallenge extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Two-factor authentication';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.two-factor-challenge';

    public ?string $code = null;

    public bool $useRecoveryCode = false;

    /**
     * Narrows auth()->user() to a concrete User.
     *
     * The page only ever renders behind the Authenticate middleware, so a null
     * here means the session died mid-request. Aborting is the correct answer:
     * the alternative is a fatal on a null property access.
     */
    private function currentUser(): User
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function mount(): void
    {
        if (session()->has(RequireTwoFactor::SESSION_KEY)) {
            redirect()->route('filament.admin.pages.dashboard');
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->label(fn () => $this->useRecoveryCode ? 'Recovery code' : 'Six-digit code')
                ->required()
                ->autocomplete('one-time-code')
                ->maxLength(20),
        ]);
    }

    public function toggleRecovery(): void
    {
        $this->useRecoveryCode = ! $this->useRecoveryCode;
        $this->code = null;
    }

    public function submit(TwoFactorService $twoFactor, AuditLogger $audit): void
    {
        $this->validate();

        $user = $this->currentUser();
        $key = '2fa-challenge:'.$user->getKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Notification::make()
                ->title('Too many attempts')
                ->body('Wait '.RateLimiter::availableIn($key).' seconds and try again.')
                ->danger()
                ->send();

            return;
        }

        RateLimiter::hit($key, 60);

        $passed = $this->useRecoveryCode
            ? $twoFactor->consumeRecoveryCode($user, (string) $this->code)
            : $twoFactor->verify((string) $user->two_factor_secret, (string) $this->code);

        if (! $passed) {
            $audit->log('auth.two_factor_failed', $user);
            logger()->channel('security')->warning('Failed 2FA attempt', [
                'user_id' => $user->getKey(),
                'ip' => request()->ip(),
            ]);

            Notification::make()->title('That code was not accepted')->danger()->send();
            $this->code = null;

            return;
        }

        RateLimiter::clear($key);

        // New session id on privilege elevation, to close any fixation window.
        session()->regenerate();
        session()->put(RequireTwoFactor::SESSION_KEY, now()->timestamp);

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->save();

        $audit->log('auth.two_factor_passed', $user);

        redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}
