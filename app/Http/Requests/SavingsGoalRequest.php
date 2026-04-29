<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavingsGoalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'target_amount'  => ['required', 'numeric', 'min:0.01'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'deadline'       => ['nullable', 'date'],
        ];
    }

    /** Ensure current_amount is never null so NOT NULL constraint is satisfied */
    protected function prepareForValidation(): void
    {
        if ($this->current_amount === null || $this->current_amount === '') {
            $this->merge(['current_amount' => 0]);
        }
    }
}
