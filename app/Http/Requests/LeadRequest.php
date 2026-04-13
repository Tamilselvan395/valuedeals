<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['required', 'string', 'max:20'],
            // 'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Please enter your mobile number.',
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter a valid email address.',
            'message.required' => 'Please enter your message.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone_code') && $this->has('phone_number') && !empty($this->phone_number)) {
            $this->merge([
                'phone' => $this->phone_code . ' ' . $this->phone_number
            ]);
        }
    }
}
