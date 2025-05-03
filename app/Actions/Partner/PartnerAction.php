<?php

namespace App\Actions\Partner;

use App\Models\Partner;
use Illuminate\Http\Request;
use App\Http\Requests\Partner\CreatePartnerFormRequest;
use App\Http\Requests\Partner\EditPartnerFormRequest;

class PartnerAction
{
    public function save(CreatePartnerFormRequest $request): Partner
    {
        $validated = $request->validated();

        $model   = new Partner($validated);

        $model->save();

        return $model;
    }

    public function update(Partner $model, EditPartnerFormRequest $request): Partner
    {
        $validated = $request->validated();

        $model->update($validated);

        return $model;
    }
}
