<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

describe('Notifications', function () {
    it('sends notification to donor and campaign creator', function () {
        Notification::fake();

        $donor = User::factory()->create();
        $creator = User::factory()->create();

        Sanctum::actingAs($donor);

        $campaign = Campaign::factory()->create(['user_id' => $creator->id]);

        $response = test()->postJson("/api/v1/campaigns/{$campaign->id}/donations", [
            'amount' => 50,
        ]);

        Notification::assertSentTo([$donor, $creator], \App\Notifications\DonationMade::class);
    });
});
