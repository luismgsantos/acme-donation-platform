<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string|null $description
 * @property-read float $goal_amount
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Support\Collection<int, \App\Models\Donation> $donations
 */
class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string|null,
     *     goal_amount: float,
     *     created_at: \Illuminate\Support\Carbon|null,
     *     creator: array{id: int, name: string},
     *     donations?: \Illuminate\Http\Resources\Json\AnonymousResourceCollection<DonationResource>
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'goal_amount' => $this->goal_amount,
            'created_at' => $this->created_at,
            'creator' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'donations' => DonationResource::collection($this->whenLoaded('donations')),
        ];
    }
}
