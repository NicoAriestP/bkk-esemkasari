<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Inertia\Inertia;
use App\Http\Requests\Questionnaire\CreateQuestionnaireFormRequest;
use App\Http\Requests\Announcement\EditAnnouncementFormRequest;
use App\Actions\Questionnaire\QuestionnaireAction;
use Symfony\Component\Console\Question\Question;

class QuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $questionnaires = Questionnaire::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%$search%");
            })
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('questionnaire/List', [
            'models' => $questionnaires,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('questionnaire/Form', [
            'model' => new Questionnaire(),
            'isNewRecord' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateQuestionnaireFormRequest $request, QuestionnaireAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('questionnaires.edit', $model->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $model)
    {
        return Inertia::render('questionnaire/Form', [
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
        EditQuestionnaireFormRequest $request,
        Questionnaire $model,
        QuestionnaireAction $action
    ) {
        $action->update($model, $request);

        return redirect()->route('questionnaires.edit', $model->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Questionnaire $model)
    {
        $model->delete();

        return redirect()->route('questionnaires.index');
    }
}
