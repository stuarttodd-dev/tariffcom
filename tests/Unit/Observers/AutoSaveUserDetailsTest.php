<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Events\UserSaved;
use App\Listeners\AutoSaveUserDetails;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoSaveUserDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_listener_creates_all_expected_user_details(): void
    {
        $user = User::factory()->create([
            'firstname' => 'John',
            'middlename' => 'Q',
            'lastname' => 'Public',
            'photo' => null,
            'prefixname' => 'Mr',
        ]);
        $listener = new AutoSaveUserDetails();

        $listener->handle(new UserSaved($user));

        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Full Name',
            'value' => 'John Q. Public',
            'icon' => '👤',
            'status' => '1',
            'type' => 'bio',
        ]);
        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Middle Initial',
            'value' => 'Q.',
            'icon' => '🔤',
            'status' => '1',
            'type' => 'bio',
        ]);
        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Avatar',
            'value' => 'No photo available',
            'icon' => '🖼️',
            'status' => '1',
            'type' => 'bio',
        ]);
        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Gender',
            'value' => 'Male',
            'icon' => '⚧',
            'status' => '1',
            'type' => 'bio',
        ]);
    }

    public function test_listener_is_attached_to_event(): void
    {
        \Illuminate\Support\Facades\Event::fake();
        \Illuminate\Support\Facades\Event::assertListening(
            \App\Events\UserSaved::class,
            \App\Listeners\AutoSaveUserDetails::class
        );
    }
} 