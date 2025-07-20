<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

it('requires authentication to access users routes', function (): void {
    $user = User::factory()->create();
    $this->get(route('users.index'))->assertRedirect('/login');
    $this->get(route('users.create'))->assertRedirect('/login');
    $this->get(route('users.show', $user))->assertRedirect('/login');
    $this->get(route('users.edit', $user))->assertRedirect('/login');
    $this->get(route('users.trashed'))->assertRedirect('/login');
});

it('lists users on index page', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertStatus(200)
        ->assertSee($user->firstname);
});

it('searches users by name or email', function (): void {
    $admin = User::factory()->create(['type' => 'admin']);
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

    $response = $this->actingAs($admin)
        ->get(route('users.index', ['search' => 'john']));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Users/Index')
        ->has('users', 1)
    );
});

it('shows single user page', function (): void {
})->todo();

it('displays user creation form', function (): void {
})->todo();

it('creates a new user with validation', function (): void {
})->todo();

it('validates email uniqueness when creating user', function (): void {
})->todo();

it('displays user edit form', function (): void {
})->todo();

it('updates user with validation', function (): void {
})->todo();

it('handles password updates correctly', function (): void {
})->todo();

it('does not update password when not provided', function (): void {
})->todo();

it('soft deletes a user', function (): void {
    $user = User::factory()->create();
    $admin = User::factory()->create(['type' => 'admin']);
    $this->actingAs($admin)
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'));
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

it('lists trashed users', function (): void {
    $user = User::factory()->create();
    $user->delete();
    $this->actingAs($user)
        ->get(route('users.trashed'))
        ->assertStatus(200)
        ->assertSee($user->firstname);
});

it('restores a soft deleted user', function (): void {
    $user = User::factory()->create();
    $user->delete();
    $this->actingAs($user)
        ->post(route('users.restore', $user->id))
        ->assertRedirect(route('users.trashed'));
    $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
});

it('permanently deletes a soft deleted user', function (): void {
    $user = User::factory()->create();
    $user->delete();
    $this->actingAs($user)
        ->delete(route('users.delete', $user->id))
        ->assertRedirect(route('users.trashed'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
