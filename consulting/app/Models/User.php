<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * Runtime property types produced by the casts below. Without these, static
 * analysis infers the raw column types (text) and cannot see through
 * `encrypted:array`.
 *
 * @property string|null $two_factor_secret
 * @property list<string>|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property Carbon|null $password_changed_at
 * @property Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property bool $is_active
 */
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Never serialised. The 2FA secret and recovery codes are encrypted at
     * rest as well (see casts) so a database-only leak does not defeat 2FA.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Gate for the Filament panel itself. Being authenticated is not enough:
     * the account must be active, verified, and hold a panel role.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active
            && $this->hasVerifiedEmail()
            && $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_EDITOR]);
    }

    public const ROLE_ADMIN = 'admin';

    public const ROLE_EDITOR = 'editor';

    public function hasTwoFactorEnabled(): bool
    {
        return filled($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }
}
