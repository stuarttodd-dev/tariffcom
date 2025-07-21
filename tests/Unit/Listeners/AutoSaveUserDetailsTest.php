<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\UserSaved;
use App\Listeners\AutoSaveUserDetails;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoSaveUserDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itSavesUserDetails(): void
    {
        $user = User::factory()->create([
            'firstname' => 'Jane',
            'middlename' => 'R',
            'lastname' => 'Doe',
            'photo' => null,
            'prefixname' => 'Ms',
        ]);
        $listener = new AutoSaveUserDetails();
        $listener->handle(new UserSaved($user));

        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Full Name',
            'value' => 'Jane R. Doe',
            'icon' => '👤',
            'status' => '1',
            'type' => 'bio',
        ]);
        $this->assertDatabaseHas('details', [
            'user_id' => $user->id,
            'key' => 'Middle Initial',
            'value' => 'R.',
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
            'value' => 'Female',
            'icon' => '⚧',
            'status' => '1',
            'type' => 'bio',
        ]);
    }
} 