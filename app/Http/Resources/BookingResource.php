<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'created_at'        => $this->created_at,
            'room'              => new RoomResource($this->room),
            'checkin'           => $this->checkin,
            'checkout'          => $this->checkout,
            'sub_total'         => (int) $this->sub_total,
            'discount'          => (int) $this->discount,
            'grand_total'       => (int) $this->grand_total,
            'number_of_night'   => $this->getTotalDay(),
            'status'            => $this->status
        ];
    }
}
