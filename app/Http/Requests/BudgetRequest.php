<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id'  => ['required', 'exists:categories,id'],
            'amount_limit' => ['required', 'numeric', 'min:0.01'],
            'month'        => ['required', 'string'],
        ];
    }
}
