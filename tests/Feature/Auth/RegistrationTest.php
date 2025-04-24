<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = test()->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = test()->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    test()->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
