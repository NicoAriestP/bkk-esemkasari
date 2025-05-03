<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\CreateUserFormRequest;
use App\Http\Requests\User\EditUserFormRequest;

class UserAction
{
    public function save(CreateUserFormRequest $request): User
    {
        $validated = $request->validated();

        $model = new User($validated);

        $model->save();

        return $model;
    }

    public function update(User $model, EditUserFormRequest $request): User
    {
        $validated = $request->validated();

        $model->update($validated);

        return $model;
    }
}
