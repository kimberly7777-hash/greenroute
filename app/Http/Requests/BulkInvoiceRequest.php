<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contractor_registration_number' => 'required|string|exists:contractors,registration_number',
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'required|exists:clients,id',
            'site_location' => 'required|array',
            'site_location.region' => 'required|string|max:255',
            'site_location.district' => 'required|string|max:255',
            'site_location.ward' => 'required|string|max:255',
            'site_location.street' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'service_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contractor_registration_number.required' => 'Contractor registration number is required.',
            'contractor_registration_number.exists' => 'Invalid contractor registration number.',
            'client_ids.required' => 'At least one client must be selected.',
            'client_ids.min' => 'Please select at least one client.',
            'client_ids.*.exists' => 'One or more selected clients are invalid.',
            'site_location.required' => 'Site location is required.',
            'site_location.region.required' => 'Region is required.',
            'site_location.district.required' => 'District is required.',
            'site_location.ward.required' => 'Ward is required.',
            'invoice_date.required' => 'Invoice date is required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date must be on or after the invoice date.',
            'service_type.required' => 'Service type is required.',
            'subtotal.required' => 'Subtotal amount is required.',
            'subtotal.min' => 'Subtotal must be a positive number.',
            'tax_rate.required' => 'Tax rate is required.',
            'tax_rate.min' => 'Tax rate cannot be negative.',
            'tax_rate.max' => 'Tax rate cannot exceed 100%.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'client_ids' => 'clients',
            'site_location.region' => 'region',
            'site_location.district' => 'district',
            'site_location.ward' => 'ward',
            'site_location.street' => 'street',
        ];
    }
}
