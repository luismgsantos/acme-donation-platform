<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DonateCampaignRequest;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Campaign;
use App\Models\Donation;
use App\Notifications\DonationMade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CampaignController extends Controller
{
    public function __construct(protected PaymentGatewayInterface $paymentGateway) {}

    /**
     * Display a listing of the campaigns.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<CampaignResource>
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
     * @return CampaignResource
     *
     * @bodyParam title string required The title of the campaign. E.g: "Save a Rubber Duck Today"
     * @bodyParam description string required The description of the campaign. E.g: "An initiative to help yellow rubber ducklings from coding."
     * @bodyParam goal_amount float required The goal amount for the campaign. E.g: 10000.00
     */
    public function store(StoreCampaignRequest $request)
    {
        $user = Auth::user();

        $campaign = $user?->campaigns()->create($request->all());

        return new CampaignResource($campaign?->load('user'));
    }

    /**
     * Display the specified campaign.
     *
     * @param  int  $id
     * @return CampaignResource
     */
    public function show($id)
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
     * @param  int  $id
     * @return CampaignResource|JsonResponse
     *
     * @bodyParam title string required The title of the campaign. E.g: "Save a Rubber Duck Today"
     * @bodyParam description string required The description of the campaign. E.g: "An initiative to help yellow rubber ducklings from coding."
     * @bodyParam goal_amount float required The goal amount for the campaign. E.g: 10000.00
     */
    public function update(UpdateCampaignRequest $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        if ($campaign->user_id !== Auth::id()) {
            return new ApiException('Unauthorized.', Response::HTTP_UNAUTHORIZED);
        }

        $campaign->update($request->all());

        return new CampaignResource($campaign);
    }

    /**
     * Allow a user to donate to a campaign.
     *
     * @param  int  $id
     * @return CampaignResource|JsonResponse
     *
     * @bodyParam amount float required The amount to donate. E.g: 50.00
     */
    public function donate(DonateCampaignRequest $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $donor = Auth::user();

        /** @var \App\Models\User|null */
        $campaignCreator = $campaign->user;

        if (! $this->paymentGateway->charge($request->amount)) {
            return new ApiException('Payment failed.', Response::HTTP_PAYMENT_REQUIRED);
        }

        /** @var Donation */
        $donation = $campaign->donations()->create([
            'user_id' => $donor?->id,
            'amount' => $request->amount,
        ]);

        $donor?->notify(new DonationMade($donation));
        $campaignCreator->notify(new DonationMade($donation));

        return new CampaignResource($campaign->load('user', 'donations'))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {
        return Inertia::render('Campaigns/Create', [
            'userToken' => auth()->user()->currentAccessToken(),
        ]);
    }
}
