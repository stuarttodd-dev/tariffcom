<?php

namespace App\Observers;

use App\Models\User;
use App\Events\UserSaved;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        UserSaved::dispatch($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        UserSaved::dispatch($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if ($user->isForceDeleting()) {
            $user->details()->forceDelete();
        }

        if (!$user->isForceDeleting()) {
            $user->details()->delete();
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $user->details()->restore();
        event(new UserSaved($user));
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        unset($user);
    }
}
