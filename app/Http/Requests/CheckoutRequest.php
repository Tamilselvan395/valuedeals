<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'payment_method'    => ['required', 'in:cod,stripe'],
            'shipping_name'    => ['required', 'string', 'max:255'],
            'shipping_phone'   => ['required', 'string', 'max:20'],
            'shipping_email'   => ['required', 'email', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city'    => ['required', 'string', 'max:100'],
            'shipping_state'   => ['required', 'string', 'max:100', 'exists:emirate_shipping_rates,slug'],
            'shipping_pincode' => ['nullable', 'string', 'max:10'],
            'shipping_country' => ['nullable', 'string', 'max:100'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required'   => 'Please choose a payment method.',
            'payment_method.in'         => 'Selected payment method is not valid.',
            'shipping_name.required'    => 'Please enter your full name.',
            'shipping_phone.required'   => 'Please enter your phone number.',
            'shipping_email.required'   => 'Please enter your email address.',
            'shipping_address.required' => 'Please enter your street address.',
            'shipping_city.required'    => 'Please enter your area or district.',
            'shipping_state.required'   => 'Please select your emirate.',
            'shipping_state.exists'     => 'Selected emirate is not valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->shipping_email && auth()->check()) {
            $this->merge(['shipping_email' => auth()->user()->email]);
        }
        if (! $this->shipping_name && auth()->check()) {
            $this->merge(['shipping_name' => auth()->user()->name]);
        }
        
        if ($this->has('phone_code') && $this->has('shipping_phone_number')) {
            $this->merge([
                'shipping_phone' => $this->phone_code . ' ' . $this->shipping_phone_number
            ]);
        }
    }
}
