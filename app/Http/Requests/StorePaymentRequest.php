<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return optional(auth('sanctum')->user()) !== null;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'room_id' => 'required|exists:rooms,id',
            'amount' => 'required|numeric|min:1|max:999999.99',
            'payment_method' => 'required|in:cash,transfer,card,check',
            'payment_date' => 'required|date|before_or_equal:today',
            'due_date' => 'required|date|after:payment_date',
            'month_year' => 'required|date_format:Y-m',
            'notes' => 'nullable|string|max:500',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka',
            'amount.min' => 'Jumlah pembayaran minimal Rp. 1',
            'due_date.after' => 'Tanggal jatuh tempo harus setelah tanggal pembayaran',
        ];
    }
}
