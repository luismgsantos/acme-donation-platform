<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Campaigns', function () {

    it('allows authenticated user to create a campaign', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = test()->postJson('/api/v1/campaigns', [
            'title' => 'Save a rubber duck',
            'description' => 'An initiative to help yellow rubber ducklings from coding.',
            'goal_amount' => 10000,
        ]);

        expect($response->status())->toBe(Response::HTTP_CREATED)
            ->and($response->json('data.title'))->toBe('Save a rubber duck');
    });

    it('allows authenticated user to view a campaign', function () {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = test()->getJson("/api/v1/campaigns/{$campaign->id}");

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->json('data.id'))->toBe($campaign->id);
    });

    it('returns 404 for non-existent campaign on view', function () {
        Sanctum::actingAs(User::factory()->create());

        $response = test()->getJson('/api/v1/campaigns/9999');

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });

    it('allows campaign update only by creator', function () {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = test()->putJson("/api/v1/campaigns/{$campaign->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'goal_amount' => 5000,
        ]);

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->json('data.title'))->toBe('Updated Title');
    });

    it('denies update by non-owner', function () {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $campaign = Campaign::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($attacker);

        $response = test()->putJson("/api/v1/campaigns/{$campaign->id}", [
            'title' => 'Hacked',
            'description' => 'Unauthorized',
        ]);

        expect($response->status())->toBe(Response::HTTP_UNAUTHORIZED)
            ->and($response->json('error'))->toBe('Unauthorized.');
    });

    it('allows campaign deletion by creator', function () {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = test()->deleteJson("/api/v1/campaigns/{$campaign->id}");

        expect($response->status())->toBe(Response::HTTP_NO_CONTENT);
    });

    it('denies campaign deletion by non-owner', function () {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $campaign = Campaign::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($attacker);

        $response = test()->deleteJson("/api/v1/campaigns/{$campaign->id}");

        expect($response->status())->toBe(Response::HTTP_UNAUTHORIZED)
            ->and($response->json('error'))->toBe('Unauthorized.');
    });

    it('returns 404 when deleting a non-existent campaign', function () {
        Sanctum::actingAs(User::factory()->create());

        $response = test()->deleteJson('/api/v1/campaigns/9999');

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND)
            ->and($response->json('error'))->toBe('Campaign not found.');
    });

    it('rejects invalid create request with missing fields', function () {
        Sanctum::actingAs(User::factory()->create());

        $response = test()->postJson('/api/v1/campaigns', []);

        expect($response->status())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->and($response->json('errors'))->toHaveKeys(['title', 'goal_amount']);
    });
});
