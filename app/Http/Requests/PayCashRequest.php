<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayCashRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cash_received' => ['required', 'numeric', 'min:0'],
        ];
    }
}
