<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

it('logs in a user and returns a token', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'password123'),
    ]);

    $response = test()->postJson('/api/auth/login', [
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

    $response = test()->postJson('/api/auth/logout');

    expect($response->status())->toBe(Response::HTTP_OK)
        ->and($response->json())->toMatchArray([
            'message' => 'Logged out',
        ])->and($user->tokens)->toBeEmpty();
});
