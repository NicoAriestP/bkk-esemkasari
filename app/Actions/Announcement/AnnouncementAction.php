<?php

namespace App\Actions\Announcement;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Requests\Announcement\CreateAnnouncementFormRequest;
use App\Http\Requests\Announcement\EditAnnouncementFormRequest;

class AnnouncementAction
{
    public function save(CreateAnnouncementFormRequest $request): Announcement
    {
        $validated = $request->validated();
        unset($validated['file']);


        $model = new Announcement($validated);

        // if (isset($request->validated()['image'])) {
        //     $model->updateImage($request->validated()['image']);
        // }

        $model->save();

        return $model;
    }

    public function update(Announcement $model, EditAnnouncementFormRequest $request): Announcement
    {
        $validated = $request->validated();
        unset($validated['file']);

        $model->update($validated);

        return $model;
    }
}
