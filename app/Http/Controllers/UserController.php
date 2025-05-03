<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Requests\User\CreateUserFormRequest;
use App\Http\Requests\User\EditUserFormRequest;
use App\Actions\User\UserAction;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            })
            ->whereNot('id', auth()->user()->id)
            ->whereNot('is_leader', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('user/List', [
            'models' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('user/Form', [
            'model' => new User(),
            'isNewRecord' => true,
        ]);
    }

    public function store(CreateUserFormRequest $request, UserAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('users.index');
    }

    public function edit(User $model)
    {
        return Inertia::render('user/Form', [
            'model' => $model,
            'isNewRecord' => false,
        ]);
    }

    public function update(EditUserFormRequest $request, User $model, UserAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('users.index');
    }

    public function destroy(User $model)
    {
        $model->delete();

        return redirect()->route('users.index');
    }
}
