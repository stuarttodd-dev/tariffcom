<?php

namespace App\Listeners;

use App\Events\UserSaved;

class AutoSaveUserDetails
{
    public function handle(UserSaved $event): void
    {
        $user = $event->user;
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
