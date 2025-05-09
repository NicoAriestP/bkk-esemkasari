<?php

namespace App\Actions\Student;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Requests\Student\CreateStudentFormRequest;
use App\Http\Requests\Student\EditStudentFormRequest;

class StudentAction
{
    public function save(CreateStudentFormRequest $request): Student
    {
        $validated = $request->validated();

        $model = new Student($validated);

        $model->save();

        return $model;
    }

    public function update(Student $model, EditStudentFormRequest $request): Student
    {
        $validated = $request->validated();

        $model->update($validated);

        return $model;
    }
}
