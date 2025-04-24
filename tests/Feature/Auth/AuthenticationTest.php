<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = test()->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = test()->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    test()->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    test()->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    test()->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = test()->actingAs($user)->post('/logout');

    test()->assertGuest();
    $response->assertRedirect('/');
});
