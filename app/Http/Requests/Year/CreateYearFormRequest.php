<?php

namespace App\Http\Requests\Year;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateYearFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        return [
            'year' => 'required|integer',
        ];
    }
}
