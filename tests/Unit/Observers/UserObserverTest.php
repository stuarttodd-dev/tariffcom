<?php

use App\Models\User;
use App\Models\Detail;
use App\Observers\UserObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UserObserver', function (): void {
    it('creates details when user is created', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'lastname' => 'Doe',
            'photo' => 'https://example.com/photo.jpg',
        ]);

        expect($user->details)->toHaveCount(4);
        
        $fullNameDetail = $user->details->where('key', 'Full Name')->first();
        $middleInitialDetail = $user->details->where('key', 'Middle Initial')->first();
        $avatarDetail = $user->details->where('key', 'Avatar')->first();
        $genderDetail = $user->details->where('key', 'Gender')->first();

        expect($fullNameDetail->value)->toBe('John M. Doe');
        expect($middleInitialDetail->value)->toBe('M.');
        expect($avatarDetail->value)->toBe('https://example.com/photo.jpg');
        expect($genderDetail->value)->toBe('Male');
    });

    it('updates details when user is updated', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);

        $user->update([
            'prefixname' => 'Mrs',
            'firstname' => 'Jane',
            'middlename' => 'Elizabeth',
            'lastname' => 'Smith',
            'photo' => 'https://example.com/new-photo.jpg',
        ]);

        $user->refresh();

        expect($user->details)->toHaveCount(4);
        
        $fullNameDetail = $user->details->where('key', 'Full Name')->first();
        $middleInitialDetail = $user->details->where('key', 'Middle Initial')->first();
        $avatarDetail = $user->details->where('key', 'Avatar')->first();
        $genderDetail = $user->details->where('key', 'Gender')->first();

        expect($fullNameDetail->value)->toBe('Jane E. Smith');
        expect($middleInitialDetail->value)->toBe('E.');
        expect($avatarDetail->value)->toBe('https://example.com/new-photo.jpg');
        expect($genderDetail->value)->toBe('Female');
    });

    it('deletes details when user is soft deleted', function (): void {
        $user = User::factory()->create();
        
        expect($user->details)->toHaveCount(4);
        
        $user->delete();

        $userWithTrashed = User::withTrashed()->find($user->id);
        expect($userWithTrashed->details)->toHaveCount(0);
    });

    it('recreates details when user is restored', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Ms',
            'firstname' => 'Alice',
            'middlename' => null,
            'lastname' => 'Johnson',
        ]);

        $user->delete();

        $userWithTrashed = User::withTrashed()->find($user->id);
        expect($userWithTrashed->details)->toHaveCount(0);

        $user->restore();

        expect($user->details)->toHaveCount(4);

        $fullNameDetail = $user->details->where('key', 'Full Name')->first();
        expect($fullNameDetail->value)->toBe('Alice Johnson');
    });

    it('handles null values correctly', function (): void {
        $user = User::factory()->create([
            'prefixname' => null,
            'middlename' => null,
            'photo' => null,
        ]);

        $fullNameDetail = $user->details->where('key', 'Full Name')->first();
        $middleInitialDetail = $user->details->where('key', 'Middle Initial')->first();
        $avatarDetail = $user->details->where('key', 'Avatar')->first();
        $genderDetail = $user->details->where('key', 'Gender')->first();

        expect($fullNameDetail->value)->toBe($user->full_name);
        expect($middleInitialDetail->value)->toBeNull();
        expect($avatarDetail->value)->toBe('No photo available');
        expect($genderDetail->value)->toBe('Unknown');
    });

    it('creates details with correct icons', function (): void {
        $user = User::factory()->create();

        $fullNameDetail = $user->details->where('key', 'Full Name')->first();
        $middleInitialDetail = $user->details->where('key', 'Middle Initial')->first();
        $avatarDetail = $user->details->where('key', 'Avatar')->first();
        $genderDetail = $user->details->where('key', 'Gender')->first();

        expect($fullNameDetail->icon)->toBe('👤');
        expect($middleInitialDetail->icon)->toBe('🔤');
        expect($avatarDetail->icon)->toBe('🖼️');
        expect($genderDetail->icon)->toBe('⚧');
    });

    it('creates details with correct status and type', function (): void {
        $user = User::factory()->create();

        foreach ($user->details as $detail) {
            expect($detail->status)->toBe('1');
            expect($detail->type)->toBe('bio');
        }
    });

    it('removes old details before creating new ones', function (): void {
        $user = User::factory()->create();
        $originalDetailCount = $user->details->count(); // Should be 4 from observer

        Detail::factory()->create([
            'user_id' => $user->id,
            'key' => 'Extra Detail',
            'value' => 'This should be removed',
        ]);

        $user->refresh();

        expect($user->details)->toHaveCount($originalDetailCount + 1); // Should be 5

        $user->update(['firstname' => 'Updated']);

        $user->refresh();

        expect($user->details)->toHaveCount(4);
        expect($user->details->where('key', 'Extra Detail'))->toHaveCount(0);
    });
}); 