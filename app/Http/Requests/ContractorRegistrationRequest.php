<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class ContractorRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'], // Removed unique - checked manually in controller
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'address' => ['required', 'string', 'max:500'],
            'site_locations' => ['nullable', 'string', 'max:1000'],
            'region' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:50'], // Removed unique - checked manually in controller
            'certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'license_plate' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'name.required' => 'Contact person name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Please provide a valid phone number.',
            'address.required' => 'Physical address is required.',
            'region.required' => 'Please select a region.',
            'district.required' => 'Please select a district.',
            'ward.required' => 'Please select a ward.',
            'license_number.required' => 'License number is required.',
            'certificate.required' => 'Please upload your license certificate.',
            'certificate.mimes' => 'Certificate must be a PDF or image file (PDF, JPG, JPEG, PNG).',
            'certificate.max' => 'Certificate file size must not exceed 2MB.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
