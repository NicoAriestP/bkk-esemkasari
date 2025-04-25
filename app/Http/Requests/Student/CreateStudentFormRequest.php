<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateStudentFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        return [
            'student_class_id' => 'nullable|exists:student_classes,id',
            'name' => 'required|string|max:50',
            'nisn' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:students,email',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:laki-laki,perempuan',
            'born_date' => 'required|date',
            'height' => 'required|string|max:5',
            'weight' => 'required|string|max:5',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'address' => 'required|string',
            'is_graduated' => 'required|boolean',
            'is_married' => 'required|boolean',
        ];
    }
}
