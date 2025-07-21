<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\UserSaved;
use App\Listeners\AutoSaveUserDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AutoSaveUserDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itHasTheCorrectListener(): void
    {
        Event::fake();
        Event::assertListening(
            UserSaved::class,
            AutoSaveUserDetails::class
        );
    }
}
