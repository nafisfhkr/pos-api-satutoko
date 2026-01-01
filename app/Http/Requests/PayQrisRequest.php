<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayQrisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference_no' => ['required', 'string', 'max:100'],
        ];
    }
}
