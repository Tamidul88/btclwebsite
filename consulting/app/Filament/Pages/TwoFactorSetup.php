<?php

namespace App\Filament\Pages;

use App\Http\Middleware\RequireTwoFactor;
use App\Models\User;
use App\Services\TwoFactorService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * TOTP enrolment.
 *
 * The candidate secret is held in the session, not the database, until a valid
 * code proves the user has it in their authenticator. Only then is it written.
 */
class TwoFactorSetup extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $title = 'Set up two-factor authentication';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.two-factor-setup';

    public ?string $code = null;

    public string $secret = '';

    public string $qrCode = '';

    /** @var list<string> */
    public array $recoveryCodes = [];

    private function currentUser(): User
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function mount(TwoFactorService $twoFactor): void
    {
        if ($this->currentUser()->hasTwoFactorEnabled()) {
            redirect()->route('filament.admin.pages.dashboard');

            return;
        }

        $this->secret = session()->get('2fa.candidate_secret') ?: $twoFactor->generateSecret();
        session()->put('2fa.candidate_secret', $this->secret);

        $this->qrCode = $twoFactor->qrCodeSvg(
            $twoFactor->provisioningUri($this->currentUser(), $this->secret)
        );
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->label('Six-digit code')
                ->required()
                ->numeric()
                ->minLength(6)
                ->maxLength(6)
                ->autocomplete('one-time-code')
                ->helperText('Enter the code shown in your authenticator app.'),
        ]);
    }

    public function confirm(TwoFactorService $twoFactor): void
    {
        $this->validate();

        $secret = session()->get('2fa.candidate_secret');

        if (! $secret || ! $twoFactor->verify($secret, (string) $this->code)) {
            Notification::make()
                ->title('That code was not accepted')
                ->body('Check your device clock and try the next code.')
                ->danger()
                ->send();

            $this->code = null;

            return;
        }

        $this->recoveryCodes = $twoFactor->generateRecoveryCodes();
        $twoFactor->confirm($this->currentUser(), $secret, $this->recoveryCodes);

        session()->forget('2fa.candidate_secret');
        session()->put(RequireTwoFactor::SESSION_KEY, now()->timestamp);

        Notification::make()
            ->title('Two-factor authentication is on')
            ->body('Save your recovery codes now — they are shown once.')
            ->success()
            ->persistent()
            ->send();
    }
}
