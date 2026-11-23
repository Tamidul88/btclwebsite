<?php

namespace App\Policies;

use App\Models\User;

/**
 * Only admins manage users. The self-protection rules below stop an admin
 * accidentally locking the organisation out of its own panel.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->is($model);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /** You cannot delete yourself, and you cannot remove the last admin. */
    public function delete(User $user, User $model): bool
    {
        if (! $user->isAdmin() || $user->is($model)) {
            return false;
        }

        if ($model->isAdmin() && User::role(User::ROLE_ADMIN)->count() <= 1) {
            return false;
        }

        return true;
    }

    /** Resetting someone else's 2FA is an admin-only, audited action. */
    public function resetTwoFactor(User $user, User $model): bool
    {
        return $user->isAdmin() && ! $user->is($model);
    }
}
