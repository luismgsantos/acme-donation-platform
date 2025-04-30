<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DonateCampaignRequest;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Campaign;
use App\Models\Donation;
use App\Notifications\DonationMade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DonateToCampaignController extends Controller
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(DonateCampaignRequest $request, int $id): CampaignResource|ApiException|JsonResponse
    {
        $campaign = Campaign::find($id);

        if (! $campaign) {
            return ApiException::notFound('Campaign not found.');
        }

        $donor = Auth::user();

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
}
