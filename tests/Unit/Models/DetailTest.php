<?php

use App\Models\Detail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Detail Model', function (): void {
    it('can create a detail with all fields', function (): void {
        $user = User::factory()->create();
        $detail = Detail::factory()->create([
            'key' => 'Full Name',
            'value' => 'John Doe',
            'icon' => '👤',
            'status' => '1',
            'type' => 'detail',
            'user_id' => $user->id,
        ]);

        expect($detail->key)->toBe('Full Name');
        expect($detail->value)->toBe('John Doe');
        expect($detail->icon)->toBe('👤');
        expect($detail->status)->toBe('1');
        expect($detail->type)->toBe('detail');
        expect($detail->user_id)->toBe($user->id);
    });

    it('has correct fillable attributes', function (): void {
        $fillable = [
            'key',
            'value',
            'icon',
            'status',
            'type',
            'user_id',
        ];

        expect((new Detail())->getFillable())->toBe($fillable);
    });

    it('belongs to a user', function (): void {
        $user = User::factory()->create();
        $detail = Detail::factory()->create(['user_id' => $user->id]);

        expect($detail->user)->toBeInstanceOf(User::class);
        expect($detail->user->id)->toBe($user->id);
    });

    it('can be created without optional fields', function (): void {
        $user = User::factory()->create();
        $detail = Detail::factory()->create([
            'key' => 'Test Key',
            'user_id' => $user->id,
            'value' => null,
            'icon' => null,
        ]);

        expect($detail->key)->toBe('Test Key');
        expect($detail->value)->toBeNull();
        expect($detail->icon)->toBeNull();
    });
});

describe('Detail Relationships', function (): void {
    it('belongs to user relationship works', function (): void {
        $user = User::factory()->create();
        $detail = Detail::factory()->create(['user_id' => $user->id]);

        $detailWithUser = Detail::with('user')->find($detail->id);

        expect($detailWithUser->user)->toBeInstanceOf(User::class);
        expect($detailWithUser->user->id)->toBe($user->id);
    });

    it('can access user through relationship', function (): void {
        $user = User::factory()->create();
        $detail = Detail::factory()->create(['user_id' => $user->id]);

        expect($detail->user->email)->toBe($user->email);
        expect($detail->user->firstname)->toBe($user->firstname);
    });
});

describe('Detail Factory', function (): void {
    it('creates valid detail records', function (): void {
        $detail = Detail::factory()->create();

        expect($detail->key)->not->toBeEmpty();
        expect($detail->status)->toBe('1');
        expect($detail->type)->toBe('detail');
    });

    it('can create multiple details', function (): void {
        $user = User::factory()->create();
        $details = Detail::factory()->count(5)->create(['user_id' => $user->id]);

        expect($details)->toHaveCount(5);
        expect($details->pluck('user_id')->unique())->toContain($user->id);
    });
}); 