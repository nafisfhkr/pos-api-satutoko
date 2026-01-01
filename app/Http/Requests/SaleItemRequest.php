<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
