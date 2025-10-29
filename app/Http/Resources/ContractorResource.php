<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorResource extends JsonResource
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
            'user_id' => $this->user_id,
            'registration_number' => $this->registration_number,
            'company_name' => $this->company_name,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'location' => [
                'region' => $this->region,
                'district' => $this->district,
                'ward' => $this->ward,
                'street' => $this->street,
                'full_address' => $this->site_location,
                'site_locations' => $this->site_locations, // Legacy field
            ],
            'license_number' => $this->license_number,
            'certificate_path' => $this->certificate_path,
            'certificate_url' => $this->certificate_path ? asset('storage/' . $this->certificate_path) : null,
            'vehicle' => [
                'type' => $this->vehicle_type,
                'license_plate' => $this->license_plate,
            ],
            'client_registration_number' => $this->client_registration_number,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
