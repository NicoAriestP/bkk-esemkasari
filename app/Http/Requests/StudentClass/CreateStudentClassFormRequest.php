<?php

namespace App\Http\Requests\StudentClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateStudentClassFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        return [
            'year_id' => 'nullable|exists:years,id',
            'name' => 'required|string|max:50',
        ];
    }
}
