<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

it('registers a user and returns a token', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($response->status())->toBe(Response::HTTP_CREATED)
        ->and($response->json())->toHaveKeys([
            'access_token',
            'token_type',
            'user' => ['id', 'name', 'email'],
        ]);
});

it('logs in a user and returns a token', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    expect($response->status())->toBe(Response::HTTP_OK)
        ->and($response->json())->toHaveKeys([
            'access_token',
            'token_type',
            'user' => ['id', 'name', 'email'],
        ]);
});

it('logs out a user and invalidates the token', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/auth/logout');

    expect($response->status())->toBe(Response::HTTP_OK)
        ->and($response->json())->toMatchArray([
            'message' => 'Logged out',
        ])->and($user->tokens)->toBeEmpty();
});
