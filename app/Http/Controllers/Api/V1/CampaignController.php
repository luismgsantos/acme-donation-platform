<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CampaignController extends Controller
{
    /**
     * Display a listing of the campaigns.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<CampaignResource>|\Inertia\Response
     */
    public function index()
    {
        $campaigns = Campaign::with(['user', 'donations'])->latest()->get();

        if (request()->wantsJson()) {
            return CampaignResource::collection($campaigns);
        }

        return Inertia::render('Campaigns/Index', [
            'campaigns' => CampaignResource::collection($campaigns),
        ]);
    }

    /**
     * Store a newly created campaign in the database.
     *
     *
     * @bodyParam title string required The title of the campaign. E.g: "Save a Rubber Duck Today"
     * @bodyParam description string required The description of the campaign. E.g: "An initiative to help yellow rubber ducklings from coding."
     * @bodyParam goal_amount float required The goal amount for the campaign. E.g: 10000.00
     */
    public function store(StoreCampaignRequest $request): CampaignResource
    {
        $user = Auth::user();

        $campaign = $user?->campaigns()->create($request->all());

        return new CampaignResource($campaign?->load('user'));
    }

    /**
     * Display the specified campaign.
     */
    public function show(int $id): CampaignResource|\Inertia\Response
    {
        $campaign = Campaign::with('user', 'donations')->findOrFail($id);

        if (request()->wantsJson()) {
            return new CampaignResource($campaign);
        }

        return Inertia::render('Campaigns/Show', [
            'campaign' => new CampaignResource($campaign),
        ]);
    }

    /**
     * Update the specified campaign in the database.
     *
     *
     * @bodyParam title string required The title of the campaign. E.g: "Save a Rubber Duck Today"
     * @bodyParam description string required The description of the campaign. E.g: "An initiative to help yellow rubber ducklings from coding."
     * @bodyParam goal_amount float required The goal amount for the campaign. E.g: 10000.00
     */
    public function update(UpdateCampaignRequest $request, int $id): CampaignResource|ApiException|JsonResponse
    {
        $campaign = Campaign::find($id);

        if (! $campaign) {
            return ApiException::notFound('Campaign not found.');
        }

        if ($campaign->user_id !== Auth::id()) {
            return ApiException::unauthorized();
        }

        $campaign->update($request->all());

        return new CampaignResource($campaign);
    }

    /**
     * Remove the specified campaign from the database.
     */
    public function destroy(int $id): JsonResponse
    {
        $campaign = Campaign::find($id);

        if (! $campaign) {
            return ApiException::notFound('Campaign not found.');
        }

        if ($campaign->user_id !== Auth::id()) {
            return ApiException::unauthorized();
        }

        $campaign->delete();

        return new JsonResponse([
            'message' => 'Campaign deleted successfully.',
        ], Response::HTTP_NO_CONTENT);
    }

    public function create(): \Inertia\Response
    {
        $user = Auth::user();

        return Inertia::render('Campaigns/Create', [
            'userToken' => $user?->currentAccessToken(),
        ]);
    }
}
