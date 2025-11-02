<?php

namespace App\Http\Requests\Questionnaire;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\QuestionType;
use Illuminate\Validation\Rule;

class CreateQuestionnaireFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'questions.*.question_title' => 'required|string',
            'questions.*.type' => ['required', 'string', Rule::enum(QuestionType::class)],
            'questions.*.notes' => 'nullable|string',
            'questions.*.options.*.option_label' => 'required_if:questions.*.type,' . QuestionType::DROPDOWN->value . ',' . QuestionType::CHECKBOX->value . '|string|max:255',
        ];
    }
}
