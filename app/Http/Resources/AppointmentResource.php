<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            '_id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'totalAmount' => $this->totalAmount,
            'time' => $this->time,
            'services' => $this->services,
            'service_id' => $this->service_id,
            'date' => $this->date
        ];
    }
}
