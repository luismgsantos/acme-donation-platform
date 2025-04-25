<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Campaigns', function () {
    it('allows authenticated user to create a campaign', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = test()->postJson('/api/v1/campaigns', [
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

        $response = test()->putJson("/api/v1/campaigns/{$campaign->id}", [
            'title' => 'Save a rubber duck',
            'description' => 'An initiative to help yellow rubber ducklings from coding.',
        ]);

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->json())->toHaveKey('data')
            ->toHaveKey('data.title', 'Save a rubber duck')
            ->toHaveKey('data.description', 'An initiative to help yellow rubber ducklings from coding.');
    });

    it('does not allow unauthorized user to create or update a campaign', function () {
        $response = test()->postJson('/api/v1/campaigns', [
            'title' => 'Save the Earth',
            'description' => 'An initiative to help the environment',
        ]);

        expect($response->status())->toBe(Response::HTTP_UNAUTHORIZED);
    });
});
