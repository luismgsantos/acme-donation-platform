<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Campaign;
use App\Models\Donation;
use App\Notifications\DonationMade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CampaignController extends Controller
{
    public function index()
    {
        return CampaignResource::collection(
            Campaign::with('user')->latest()->get()
        );
    }

    /**
     * Store a newly created campaign in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return CampaignResource
     *
     * @bodyParam title string required The title of the campaign. E.g: "Save a Rubber Duck Today"
     * @bodyParam description string required The description of the campaign. E.g: "An initiative to help yellow rubber ducklings from coding."
     * @bodyParam goal_amount float required The goal amount for the campaign. E.g: 10000.00
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_amount' => 'required|numeric|min:1',
        ]);

        $campaign = Auth::user()->campaigns()->create($validated);

        return new CampaignResource($campaign->load('user'));
    }

    public function show($id)
    {
        $campaign = Campaign::with('user', 'donations')->findOrFail($id);

        return new CampaignResource($campaign);
    }

    /**
     * Update the specified campaign in the database.
     *
     * @param int $id
     *
     * @bodyParam title string The title of the campaign. E.g: "Save a Rubber Duck Today"
     * @bodyParam description string The description of the campaign. E.g: "An initiative to help yellow rubber ducklings from coding."
     * @bodyParam goal_amount float The goal amount for the campaign. E.g: 10000.00
     */
    public function update($id)
    {
        $campaign = Campaign::findOrFail($id);

        if ($campaign->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $validated = request()->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'goal_amount' => 'numeric|min:1',
        ]);

        $campaign->update($validated);

        return new CampaignResource($campaign);
    }

    /**
     * Allow a user to donate to a campaign.
     *
     * @param int $id
     *
     * @bodyParam amount float required The amount to donate. E.g: 50.00
     */
    public function donate($id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = request()->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $donor = Auth::user();
        $campaignCreator = $campaign->user;


        /** @var Donation */
        $donation = $campaign->donations()->create([
            'user_id' => $donor->id,
            'amount' => $validated['amount'],
        ]);

        $donor->notify(new DonationMade($donation));
        $campaignCreator->notify(new DonationMade($donation));

        return new CampaignResource($campaign->load('user', 'donations'))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
