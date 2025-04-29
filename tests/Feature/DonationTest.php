<?php

namespace Tests\Feature;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Donations', function () {
    it('allows authenticated user to donate to a campaign', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $campaign = Campaign::factory()->create();

        $response = test()->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
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

        $response = test()->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
            'amount' => 50,
        ]);

        expect($response->status())->toBe(Response::HTTP_UNAUTHORIZED);
    });

    it('validates donation amount is greater than zero', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $campaign = Campaign::factory()->create();

        $response = test()->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
            'amount' => 0,
        ]);

        expect($response->status())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->and($response->json())->toHaveKey('errors')
            ->toHaveKey('errors.amount');
    });

    it('charges the donor through the payment gateway when donating', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $campaign = Campaign::factory()->create();

        $mock = Mockery::mock(PaymentGatewayInterface::class);
        $mock->shouldReceive('charge')
            ->once()
            ->with(50)
            ->andReturnTrue();

        app()->instance(PaymentGatewayInterface::class, $mock);

        $response = test()->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
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
});
