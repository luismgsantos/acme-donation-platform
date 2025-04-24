<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DonationResource;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Auth::user()
            ->donations()
            ->with('campaign')
            ->latest()
            ->get();

        return DonationResource::collection($donations);
    }

    /**
     * Donate to a specific campaign.
     *
     * @param \Illuminate\Http\Request $request
     * @return DonationResource
     *
     * @bodyParam campaign_id int required The ID of the campaign to donate to. E.g: 1
     * @bodyParam amount float required The donation amount. E.g: 50.00
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $donation = Auth::user()->donations()->create($validated);

        return new DonationResource($donation->load('user'));
    }

    public function byCampaign($id)
    {
        $campaign = Campaign::findOrFail($id);

        $donations = $campaign->donations()->with('user')->latest()->get();

        return DonationResource::collection($donations);
    }
}
