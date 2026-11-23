<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Creates the first (or an additional) panel administrator.
 *
 * The password is prompted for, never accepted as an argument: arguments land
 * in shell history and in the process list where any local user can read them.
 * 2FA is not set here — the account must enrol on first login.
 */
class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {--name= : Display name}
                            {--email= : Email address}';

    protected $description = 'Create an administrator for the panel';

    public function handle(AuditLogger $audit): int
    {
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email address');
        $password = $this->secret('Password (min 12 chars, mixed case, number, symbol)');
        $confirm = $this->secret('Confirm password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirm,
        ], [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:filter', 'max:190', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->forceFill([
            // Verified explicitly: this account is created by someone with
            // shell access, so the email round-trip adds nothing.
            'email_verified_at' => now(),
            'password_changed_at' => now(),
            'is_active' => true,
        ])->save();

        $user->syncRoles([User::ROLE_ADMIN]);

        $audit->log('user.created_via_console', $user, ['email' => $email]);

        $this->info("Administrator created: {$email}");
        $this->warn('Two-factor authentication must be set up on first login before the panel unlocks.');
        $this->line('Panel: '.url('/'.config('security.admin_path')));

        return self::SUCCESS;
    }
}
