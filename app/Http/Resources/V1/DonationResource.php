<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read float $amount
 * @property-read \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User $user
 */
class DonationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array{
     *     id: int,
     *     amount: float,
     *     donated_at: \Illuminate\Support\Carbon,
     *     donor: array{id: int, name: string}
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'amount'     => $this->amount,
            'donated_at' => $this->created_at,
            'donor'      => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
