<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditUserFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        $id = $this->segment(2);

        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ];
    }
}
