<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:read,App\Models\User');
        $this->middleware('can:create,App\Models\User')->only('store');
        $this->middleware('can:update,App\Models\User')->only('update');
        $this->middleware('can:delete,App\Models\User')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = User::query();

        if ($request->user()->can('read-roles')) {
            $model = $model->with('roles');
        }

        $q = $request->input('q');
        if ($q) {
            $model = $model
                ->where('username', 'like', "%$q%")
                ->orWhere('name', 'like', "%$q%");
        }

        if ($request->input('trashed') == 'true') {
            $model = $model->withTrashed();
        }

        if ($request->has('page')) {
            return $model->paginate(20);
        }

        return $model->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        return User::create([
            'name' => $request->input('name'),
            'username' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($request->user()->can('read-roles')) {
            return User::with('roles')->findOrFail($id);
        } else {
            return User::findOrFail($id);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255|unique:users',
            'password' => 'nullable|string|min:8',
            'roles' => 'nullable|array',
        ]);

        $user = User::findOrFail($id);

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        if ($request->has('password')) {
            $user->name = Hash::make($request->input('password'));
        }

        if ($request->user()->can('read-roles') && $request->has('roles')) {
            $roles = collect($request->input('roles'));

            if ($id !== $request->user()->id) {
                Bouncer::sync($user)->roles(
                    $roles->map(function ($role) {
                        return $role['name'];
                    })
                );
            }
        }

        $user->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($id == $request->user()->id) {
            abort(400);
        }

        $user = User::withTrashed()->findOrFail($id);

        if ($request->input('destroy') === 'true' && $user->trashed()) {
            $user->forceDelete();
        } else {
            $user->delete();
        }
    }
}
