<?php

namespace App\Http\Requests\Vacancy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditVacancyFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ];
    }
}
