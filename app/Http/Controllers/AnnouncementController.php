<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\Announcement\CreateAnnouncementFormRequest;
use App\Http\Requests\Announcement\EditAnnouncementFormRequest;
use App\Actions\Announcement\AnnouncementAction;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $announcements = Announcement::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%$search%");
            })
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('announcement/List', [
            'models' => $announcements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('announcement/Form', [
            'model' => new Announcement(),
            'isNewRecord' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAnnouncementFormRequest $request, AnnouncementAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('announcements.edit', $model->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $model)
    {
        return Inertia::render('announcement/Form', [
            'model' => $model,
            'isNewRecord' => false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(
        EditAnnouncementFormRequest $request,
        Announcement $model,
        AnnouncementAction $action
    ) {
        $action->update($model, $request);

        return redirect()->route('announcements.edit', $model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $model)
    {
        $model->delete();

        return redirect()->route('announcements.index');
    }
}
