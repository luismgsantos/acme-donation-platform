<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

it('allows authenticated user to create a campaign', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/campaigns', [
        'title' => 'Save the Earth',
        'description' => 'An initiative to help the environment',
        'goal_amount' => 10000,
    ]);

    expect($response->status())->toBe(Response::HTTP_CREATED)
        ->and($response->json())->toHaveKey('data')
        ->and(Arr::get($response->json(), 'data'))->toHaveKeys([
            'title',
            'description',
            'goal_amount',
            'creator',
        ]);
});

it('allows authenticated user to update a campaign', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $campaign = Campaign::factory()->create(['user_id' => $user->id]);

    $response = $this->putJson("/api/v1/campaigns/{$campaign->id}", [
        'title' => 'Save a rubber duck',
        'description' => 'An initiative to help yellow rubber ducklings from coding.'
    ]);

    expect($response->status())->toBe(Response::HTTP_OK)
        ->and($response->json())->toHaveKey('data')
        ->toHaveKey('data.title', 'Save a rubber duck')
        ->toHaveKey('data.description', 'An initiative to help yellow rubber ducklings from coding.');
});

it('does not allow unauthorized user to create or update a campaign', function () {
    $response = $this->postJson('/api/v1/campaigns', [
        'title' => 'Save the Earth',
        'description' => 'An initiative to help the environment',
    ]);

    expect($response->status())->toBe(Response::HTTP_UNAUTHORIZED);
});

it('allows authenticated user to donate to a campaign', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $campaign = Campaign::factory()->create();

    $response = $this->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
        'amount' => 50,
    ]);

    expect($response->status())->toBe(Response::HTTP_CREATED)
        ->and($response->json())->toHaveKey('data')
        ->toHaveKeys([
            'data.title',
            'data.description',
            'data.goal_amount',
            'data.creator',
            'data.donations',
        ])
        ->and(Arr::get($response->json(), 'data.donations'))->toHaveCount(1)
        ->and(Arr::get($response->json(), 'data.donations.0.amount'))->toBe(50);
});

it('does not allow unauthorized user to donate', function () {
    $campaign = Campaign::factory()->create();

    $response = $this->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
        'amount' => 50,
    ]);

    expect($response->status())->toBe(Response::HTTP_UNAUTHORIZED);
});

it('validates donation amount is greater than zero', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $campaign = Campaign::factory()->create();

    $response = $this->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
        'amount' => 0,
    ]);

    expect($response->status())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($response->json())->toHaveKey('errors')
        ->toHaveKey('errors.amount');
});

it('sends notification to donor and campaign creator', function () {
    Notification::fake();

    $donor = User::factory()->create();
    $creator = User::factory()->create();

    Sanctum::actingAs($donor);

    $campaign = Campaign::factory()->create(['user_id' => $creator->id]);

    $response = $this->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
        'amount' => 50,
    ]);

    Notification::assertSentTo([$donor, $creator], \App\Notifications\DonationMade::class);
});
