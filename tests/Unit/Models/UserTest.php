<?php

use App\Models\User;
use App\Models\Detail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User Model', function (): void {
    it('can create a user with all fields', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'lastname' => 'Doe',
            'suffixname' => 'Jr',
            'email' => 'john@example.com',
            'type' => 'admin',
        ]);

        expect($user->prefixname)->toBe('Mr');
        expect($user->firstname)->toBe('John');
        expect($user->lastname)->toBe('Doe');
        expect($user->email)->toBe('john@example.com');
        expect($user->type)->toBe('admin');
    });

    it('has correct fillable attributes', function (): void {
        $fillable = [
            'prefixname',
            'firstname',
            'middlename',
            'lastname',
            'suffixname',
            'email',
            'password',
            'photo',
            'type',
        ];

        expect((new User())->getFillable())->toBe($fillable);
    });

    it('has correct hidden attributes', function (): void {
        $hidden = [
            'password',
            'remember_token',
        ];

        expect((new User())->getHidden())->toBe($hidden);
    });

    it('has correct casts', function (): void {
        $casts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];

        expect((new User())->getCasts())->toHaveKey('email_verified_at', 'datetime');
        expect((new User())->getCasts())->toHaveKey('password', 'hashed');
    });
});

describe('User Accessors', function (): void {
    it('generates full name correctly', function (): void {
        $user = User::factory()->create([
            'firstname' => 'John',
            'middlename' => 'Michael',
            'lastname' => 'Doe',
        ]);

        expect($user->full_name)->toBe('John Michael Doe');
    });

    it('generates full name without middle name', function (): void {
        $user = User::factory()->create([
            'firstname' => 'John',
            'middlename' => null,
            'lastname' => 'Doe',
        ]);

        expect($user->full_name)->toBe('John Doe');
    });

    it('generates middle initial correctly', function (): void {
        $user = User::factory()->create([
            'middlename' => 'Michael',
        ]);

        expect($user->middle_initial)->toBe('M.');
    });

    it('returns null for middle initial when no middle name', function (): void {
        $user = User::factory()->create([
            'middlename' => null,
        ]);

        expect($user->middle_initial)->toBeNull();
    });

    it('determines gender correctly for Mr', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Mr',
        ]);

        expect($user->gender)->toBe('Male');
    });

    it('determines gender correctly for Mrs', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Mrs',
        ]);

        expect($user->gender)->toBe('Female');
    });

    it('determines gender correctly for Ms', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Ms',
        ]);

        expect($user->gender)->toBe('Female');
    });

    it('returns unknown for invalid prefix', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Dr',
        ]);

        expect($user->gender)->toBe('Unknown');
    });
});

describe('User Relationships', function (): void {
    it('has many details', function (): void {
        $user = User::factory()->create();
        $details = Detail::factory()->count(3)->create(['user_id' => $user->id]);

        expect($user->details)->toHaveCount(7); // 4 from observer + 3 manual
        expect($user->details->first())->toBeInstanceOf(Detail::class);
    });

    it('can load details relationship', function (): void {
        $user = User::factory()->create();
        Detail::factory()->create(['user_id' => $user->id]);

        $userWithDetails = User::with('details')->find($user->id);

        expect($userWithDetails->details)->toHaveCount(5); // 4 from observer + 1 manual
    });
});

describe('User Scopes', function (): void {
    it('filters admin users', function (): void {
        User::factory()->create(['type' => 'admin']);
        User::factory()->create(['type' => 'user']);
        User::factory()->create(['type' => 'admin']);

        $admins = User::admins()->get();

        expect($admins)->toHaveCount(2);
        expect($admins->pluck('type')->unique())->toContain('admin');
    });

    it('filters regular users', function (): void {
        User::factory()->create(['type' => 'admin']);
        User::factory()->create(['type' => 'user']);
        User::factory()->create(['type' => 'user']);

        $users = User::regularUsers()->get();

        expect($users)->toHaveCount(2);
        expect($users->pluck('type')->unique())->toContain('user');
    });

    it('filters users by prefix', function (): void {
        User::factory()->create(['prefixname' => 'Mr']);
        User::factory()->create(['prefixname' => 'Mrs']);
        User::factory()->create(['prefixname' => 'Mr']);

        $mrUsers = User::withPrefix('Mr')->get();

        expect($mrUsers)->toHaveCount(2);
        expect($mrUsers->pluck('prefixname')->unique())->toContain('Mr');
    });

    it('searches users by name or email', function (): void {
        User::factory()->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
        ]);
        User::factory()->create([
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'jane@example.com',
        ]);

        $searchResults = User::search('john')->get();

        expect($searchResults)->toHaveCount(1);
        expect($searchResults->first()->firstname)->toBe('John');
    });

    it('filters verified users', function (): void {
        User::factory()->create(['email_verified_at' => now()]);
        User::factory()->create(['email_verified_at' => null]);
        User::factory()->create(['email_verified_at' => now()]);

        $verifiedUsers = User::verified()->get();

        expect($verifiedUsers)->toHaveCount(2);
        expect($verifiedUsers->whereNull('email_verified_at'))->toHaveCount(0);
    });

    it('filters unverified users', function (): void {
        User::factory()->create(['email_verified_at' => now()]);
        User::factory()->create(['email_verified_at' => null]);
        User::factory()->create(['email_verified_at' => null]);

        $unverifiedUsers = User::unverified()->get();

        expect($unverifiedUsers)->toHaveCount(2);
        expect($unverifiedUsers->whereNotNull('email_verified_at'))->toHaveCount(0);
    });

    it('filters users with photos', function (): void {
        User::factory()->create(['photo' => 'https://example.com/photo.jpg']);
        User::factory()->create(['photo' => null]);
        User::factory()->create(['photo' => 'https://example.com/photo2.jpg']);

        $usersWithPhotos = User::withPhotos()->get();

        expect($usersWithPhotos)->toHaveCount(2);
        expect($usersWithPhotos->whereNull('photo'))->toHaveCount(0);
    });
});

describe('User Soft Deletes', function (): void {
    it('can soft delete a user', function (): void {
        $user = User::factory()->create();
        $user->delete();

        expect(User::find($user->id))->toBeNull();
    });

    it('can restore a soft deleted user', function (): void {
        $user = User::factory()->create();
        $user->delete();

        $user->restore();

        expect(User::find($user->id))->not->toBeNull();
        expect($user->fresh()->deleted_at)->toBeNull();
    });

    it('can permanently delete a user', function (): void {
        $user = User::factory()->create();
        $user->delete();

        $user->forceDelete();

        expect(User::withTrashed()->find($user->id))->toBeNull();
    });
});