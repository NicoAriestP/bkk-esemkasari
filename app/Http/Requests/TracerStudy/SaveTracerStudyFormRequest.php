<?php

namespace App\Http\Requests\TracerStudy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveTracerStudyFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        return [
            'is_married' => 'required|boolean',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'height' => 'required|numeric|max:5',
            'weight' => 'required|numeric|max:5',
            'cv_file' => 'required|file|mimes:pdf|max:2048',
            'student_activity_answers' => 'required|json',
            'detail_activity_answers' => 'required|json',
            'student_working_answers' => 'nullable|json',
            'student_university_answers' => 'nullable|json',
            'student_entrepreneur_answers' => 'nullable|json',
            'student_feedback_answers' => 'required|json',
        ];
    }
}
