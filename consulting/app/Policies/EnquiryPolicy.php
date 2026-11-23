<?php

namespace App\Policies;

use App\Models\Enquiry;
use App\Models\User;

/**
 * Enquiries are created by the public and only ever read/managed by staff.
 * There is deliberately no `create` or `update` for the panel: an admin
 * editing a submitted enquiry would destroy its evidential value.
 */
class EnquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EDITOR]);
    }

    public function view(User $user, Enquiry $enquiry): bool
    {
        return $this->viewAny($user);
    }

    public function downloadAttachment(User $user, Enquiry $enquiry): bool
    {
        return $user->is_active
            && $enquiry->hasAttachment()
            && $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EDITOR]);
    }

    /** Marking handled is the only mutation staff may perform. */
    public function markHandled(User $user, Enquiry $enquiry): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Enquiry $enquiry): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Enquiry $enquiry): bool
    {
        return false;
    }
}
