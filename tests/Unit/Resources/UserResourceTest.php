<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Detail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UserResource', function (): void {
    it('transforms user data correctly', function (): void {
        $user = User::factory()->create([
            'prefixname' => 'Mr',
            'firstname' => 'John',
            'middlename' => 'Michael',
            'lastname' => 'Doe',
            'suffixname' => 'Jr',
            'email' => 'john@example.com',
            'photo' => 'https://example.com/photo.jpg',
            'type' => 'admin',
        ]);

        $resource = new UserResource($user);
        $data = $resource->toArray(request());

        expect($data['id'])->toBe($user->id);
        expect($data['prefixname'])->toBe('Mr');
        expect($data['firstname'])->toBe('John');
        expect($data['middlename'])->toBe('Michael');
        expect($data['lastname'])->toBe('Doe');
        expect($data['suffixname'])->toBe('Jr');
        expect($data['email'])->toBe('john@example.com');
        expect($data['photo'])->toBe('https://example.com/photo.jpg');
        expect($data['type'])->toBe('admin');
        expect($data['full_name'])->toBe('John M. Doe');
        expect($data['middle_initial'])->toBe('M.');
        expect($data['gender'])->toBe('Male');
    });

    it('includes computed attributes', function (): void {
        $user = User::factory()->create([
            'firstname' => 'Jane',
            'middlename' => null,
            'lastname' => 'Smith',
            'prefixname' => 'Ms',
        ]);

        $resource = new UserResource($user);
        $data = $resource->toArray(request());

        expect($data['full_name'])->toBe('Jane Smith');
        expect($data['middle_initial'])->toBeNull();
        expect($data['gender'])->toBe('Female');
    });

    it('includes details when loaded', function (): void {
        $user = User::factory()->create();
        $detail = Detail::factory()->create(['user_id' => $user->id]);
        $user->load('details');

        $resource = new UserResource($user);
        $data = $resource->toArray(request());

        expect($data['details'])->toHaveCount(5); // 4 from observer + 1 manual
        expect($data['details']->where('id', $detail->id)->first()['key'])->toBe($detail->key);
    });

    it('does not include details when not loaded', function (): void {
        $user = User::factory()->create();
        Detail::factory()->create(['user_id' => $user->id]);

        $resource = new UserResource($user);
        $data = $resource->toArray(request());

        expect($data['details'])->toBeInstanceOf(\Illuminate\Http\Resources\MissingValue::class);
    });

    it('handles null values correctly', function (): void {
        $user = User::factory()->create([
            'prefixname' => null,
            'middlename' => null,
            'suffixname' => null,
            'photo' => null,
        ]);

        $resource = new UserResource($user);
        $data = $resource->toArray(request());

        expect($data['prefixname'])->toBeNull();
        expect($data['middlename'])->toBeNull();
        expect($data['suffixname'])->toBeNull();
        expect($data['photo'])->toBeNull();
        expect($data['middle_initial'])->toBeNull();
        expect($data['gender'])->toBe('Unknown');
    });

    it('includes timestamps', function (): void {
        $user = User::factory()->create();

        $resource = new UserResource($user);
        $data = $resource->toArray(request());

        expect($data)->toHaveKey('created_at');
        expect($data)->toHaveKey('updated_at');
        expect($data['created_at'])->toBeInstanceOf(\Carbon\Carbon::class);
        expect($data['updated_at'])->toBeInstanceOf(\Carbon\Carbon::class);
    });

    it('includes soft delete timestamp when applicable', function (): void {
        $user = User::factory()->create();
        $user->delete();

        $resource = new UserResource($user);
        $data = $resource->toArray(request());

        expect($data['deleted_at'])->toBeInstanceOf(\Carbon\Carbon::class);
    });
}); 