<?php

namespace App\Actions\StudentClass;

use App\Models\StudentClass;
use Illuminate\Http\Request;
use App\Http\Requests\StudentClass\CreateStudentClassFormRequest;
use App\Http\Requests\StudentClass\EditStudentClassFormRequest;

class StudentClassAction
{
    public function save(CreateStudentClassFormRequest $request): StudentClass
    {
        $validated = $request->validated();

        $model = new StudentClass($validated);

        $model->save();

        return $model;
    }

    public function update(StudentClass $model, EditStudentClassFormRequest $request): StudentClass
    {
        $validated = $request->validated();

        $model->update($validated);

        return $model;
    }
}
