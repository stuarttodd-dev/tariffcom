<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->saveUserDetails($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->saveUserDetails($user);
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
        $this->saveUserDetails($user);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        unset($user);
    }

    /**
     * Save user details to the details table.
     */
    private function saveUserDetails(User $user): void
    {
        $user->details()->forceDelete();

        $details = [
            [
                'key' => 'Full Name',
                'value' => $user->full_name,
                'icon' => '👤',
                'status' => '1',
                'type' => 'bio',
            ],
            [
                'key' => 'Middle Initial',
                'value' => $user->middle_initial,
                'icon' => '🔤',
                'status' => '1',
                'type' => 'bio',
            ],
            [
                'key' => 'Avatar',
                'value' => $user->photo ?: 'No photo available',
                'icon' => '🖼️',
                'status' => '1',
                'type' => 'bio',
            ],
            [
                'key' => 'Gender',
                'value' => $user->gender,
                'icon' => '⚧',
                'status' => '1',
                'type' => 'bio',
            ],
        ];

        foreach ($details as $detail) {
            $user->details()->create($detail);
        }
    }
}
