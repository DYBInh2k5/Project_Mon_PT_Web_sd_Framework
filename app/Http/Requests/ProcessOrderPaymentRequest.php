<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessOrderPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', Rule::in(['credit_card', 'bank_transfer', 'e_wallet'])],
            'cardholder_name' => ['required', 'string', 'max:100'],
            'card_last_four' => ['required', 'digits:4'],
            'customer_email' => ['required', 'email', 'max:255'],
        ];
    }
}
