<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ImportStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                File::types(['csv', 'xls', 'xlsx'])
                    ->max(1024 * 10), // 10MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File import tidak boleh kosong',
            'file.mimes' => 'Format file harus: csv, xls, atau xlsx',
            'file.max' => 'Ukuran file maksimal 10MB',
        ];
    }
}
