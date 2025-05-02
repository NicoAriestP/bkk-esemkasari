<?php

namespace App\Http\Requests\StudentClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditStudentClassFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'year_id' => 'nullable|exists:years,id',
        ];
    }
}
