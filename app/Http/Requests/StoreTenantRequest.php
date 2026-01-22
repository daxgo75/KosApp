<?php

namespace App\Http\Requests;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return optional(auth('sanctum')->user()) !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:tenants|max:255',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$|min:10|max:20',
            'identity_type' => 'required|in:ktp,sim,passport',
            'identity_number' => 'required|string|unique:tenants|min:10|max:50',
            'address' => 'required|string|min:5|max:500',
            'city' => 'required|string|min:3|max:100',
            'province' => 'required|string|min:3|max:100',
            'postal_code' => 'required|regex:/^\d{5}(-\d{4})?$/',
            'birth_date' => 'nullable|date|before:today',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah terdaftar di sistem',
            'identity_number.unique' => 'Nomor identitas sudah terdaftar',
            'phone.regex' => 'Format nomor telepon tidak valid',
            'postal_code.regex' => 'Format kode pos tidak valid',
            'birth_date.before' => 'Tanggal lahir harus di masa lalu',
        ];
    }
}
