<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Partner;
use App\Http\Requests\Partner\CreatePartnerFormRequest;
use App\Http\Requests\Partner\EditPartnerFormRequest;
use App\Actions\Partner\PartnerAction;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $partners = Partner::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('partner/List', [
            'models' => $partners,
        ]);
    }

    public function create()
    {
        return Inertia::render('partner/Form', [
            'model' => new Partner(),
            'isNewRecord' => true,
        ]);
    }

    public function store(CreatePartnerFormRequest $request, PartnerAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('partners.index');
    }

    public function edit(Partner $model)
    {
        return Inertia::render('partner/Form', [
            'model' => $model,
            'isNewRecord' => false,
        ]);
    }

    public function update(EditPartnerFormRequest $request, Partner $model, PartnerAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('partners.index');
    }

    public function destroy(Partner $model)
    {
        $model->delete();

        return redirect()->route('partners.index');
    }
}
