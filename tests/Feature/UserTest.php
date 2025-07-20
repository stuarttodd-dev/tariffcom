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
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('users.show', $user))
        ->assertStatus(200)
        ->assertSee($user->email);
});

it('displays user creation form', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('users.create'))
        ->assertStatus(200);
});

it('creates a new user with validation', function (): void {
    $user = User::factory()->create();
    $postData = [
        'prefixname' => 'Mr',
        'firstname' => 'John',
        'middlename' => 'Q',
        'lastname' => 'Public',
        'suffixname' => null,
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'type' => 'user',
    ];
    $this->actingAs($user)
        ->post(route('users.store'), $postData)
        ->assertRedirect(route('users.show', 2)); // New user will have ID 2
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});

it('displays user edit form', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('users.edit', $user))
        ->assertStatus(200);
});

it('updates user with validation', function (): void {
    $user = User::factory()->create();
    $updateData = [
        'prefixname' => 'Ms',
        'firstname' => 'Jane',
        'middlename' => 'A',
        'lastname' => 'Doe',
        'suffixname' => 'Jr',
        'email' => $user->email,
        'type' => 'admin',
    ];
    $this->actingAs($user)
        ->put(route('users.update', $user), $updateData)
        ->assertRedirect(route('users.show', $user));
    $this->assertDatabaseHas('users', ['firstname' => 'Jane', 'type' => 'admin']);
});

it('handles password updates correctly', function (): void {
    $admin = User::factory()->create(['type' => 'admin']);
    $user = User::factory()->create();

    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertRedirect(route('users.show', $user));

    $user->refresh();
    expect($user->password)->not->toBe('newpassword123');
});

it('does not update password when not provided', function (): void {
    $admin = User::factory()->create(['type' => 'admin']);
    $user = User::factory()->create();
    $originalPassword = $user->password;

    $response = $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'firstname' => 'Updated',
            'lastname' => $user->lastname,
            'email' => $user->email,
        ]);

    $response->assertRedirect(route('users.show', $user));

    $user->refresh();
    expect($user->password)->toBe($originalPassword);
});

it('validates email uniqueness when creating user', function (): void {
    $admin = User::factory()->create(['type' => 'admin']);
    User::factory()->create(['email' => 'john@example.com']);

    $response = $this->actingAs($admin)
        ->post(route('users.store'), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    $response->assertSessionHasErrors(['email']);
});

it('validates prefixname values', function (): void {
    $admin = User::factory()->create(['type' => 'admin']);

    $response = $this->actingAs($admin)
        ->post(route('users.store'), [
            'prefixname' => 'Invalid',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    $response->assertSessionHasErrors(['prefixname']);
});

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
