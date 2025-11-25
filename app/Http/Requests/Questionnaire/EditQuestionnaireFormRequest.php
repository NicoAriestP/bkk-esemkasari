<?php

namespace App\Http\Requests\Questionnaire;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\QuestionType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EditQuestionnaireFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_at' => 'nullable|date',
            'questions' => 'required|array|min:1',
            'questions.*.question_title' => 'required|string|max:500',
            'questions.*.type' => ['required', 'string', Rule::enum(QuestionType::class)],
            'questions.*.notes' => 'nullable|string',
            'questions.*.options' => 'required_if:questions.*.type,' . QuestionType::DROPDOWN->value . ',' . QuestionType::CHECKBOX->value . '|array',
            'questions.*.options.*.option_label' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul kuesioner harus diisi',
            'title.max' => 'Judul kuesioner maksimal 255 karakter',
            'description.required' => 'Deskripsi kuesioner harus diisi',
            'due_at.date' => 'Format tanggal batas waktu tidak valid',
            'questions.required' => 'Minimal harus ada 1 pertanyaan',
            'questions.min' => 'Minimal harus ada 1 pertanyaan',
            'questions.*.question_title.required' => 'Pertanyaan harus diisi',
            'questions.*.question_title.max' => 'Pertanyaan maksimal 500 karakter',
            'questions.*.type.required' => 'Tipe pertanyaan harus dipilih',
            'questions.*.options.required_if' => 'Opsi jawaban harus diisi untuk tipe Pilihan atau Checkbox',
            'questions.*.options.*.option_label.required' => 'Label opsi harus diisi',
            'questions.*.options.*.option_label.max' => 'Label opsi maksimal 255 karakter',
        ];
    }
}
