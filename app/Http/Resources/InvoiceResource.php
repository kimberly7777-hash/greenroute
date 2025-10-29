<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'contractor' => [
                'id' => $this->contractor_id,
                'registration_number' => $this->contractor_registration_number,
                'name' => $this->contractor?->name,
            ],
            'client' => [
                'id' => $this->client_id,
                'registration_number' => $this->client_registration_number,
                'name' => $this->client?->name,
            ],
            'schedule_id' => $this->schedule_id,
            'invoice_date' => $this->invoice_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'status' => $this->status,
            'financial' => [
                'subtotal' => (float) $this->subtotal,
                'tax_rate' => (float) $this->tax_rate,
                'tax_amount' => (float) $this->tax_amount,
                'total_amount' => (float) $this->total_amount,
                'amount_paid' => (float) $this->amount_paid,
                'balance_due' => (float) $this->balance_due,
            ],
            'service_type' => $this->service_type,
            'description' => $this->description,
            'notes' => $this->notes,
            'payment' => [
                'method' => $this->payment_method,
                'paid_at' => $this->paid_at?->toISOString(),
                'is_paid' => $this->is_paid,
                'is_overdue' => $this->is_overdue,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
