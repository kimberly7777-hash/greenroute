<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'registration_number' => $this->registration_number,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_2' => $this->phone_2,
            'phone_3' => $this->phone_3,
            'email_2' => $this->email_2,
            'email_3' => $this->email_3,
            'address' => $this->address,
            'location' => [
                'region' => $this->region,
                'district' => $this->district,
                'ward' => $this->ward,
                'street' => $this->street,
                'full_address' => $this->site_location,
            ],
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'category' => $this->category,
            'contact_name' => $this->contact_name,
            'route' => $this->route,
            'route_sequence' => $this->route_sequence,
            'notes' => $this->notes,
            'contractor_id' => $this->contractor_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
