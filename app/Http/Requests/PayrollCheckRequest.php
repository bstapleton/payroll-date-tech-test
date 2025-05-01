<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        // No faffing around with auth for this one!
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }
}
