<?php

use App\Models\User;

test('profile page is displayed', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function (): void {
    $user = User::factory()->create(['middlename' => null]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'firstname' => 'Test',
            'lastname' => 'User',
            'middlename' => null,
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->full_name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertTrue($user->fresh()->trashed());
});

test('correct password must be provided to delete account', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('profile update fails if email is already taken by another user', function (): void {
    $user = \App\Models\User::factory()->create([
        'email' => 'user1@example.com',
    ]);
    $otherUser = \App\Models\User::factory()->create([
        'email' => 'user2@example.com',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $otherUser->email, // Try to use another user's email
        ]);

    $response->assertSessionHasErrors('email');

    $this->assertSame('user1@example.com', $user->fresh()->email);
});
