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
        $this->middleware('can:read-users');
        $this->middleware('can:create-users')->only('store');
        $this->middleware('can:update-users')->only('update');
        $this->middleware('can:delete-users')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return User::with('roles')->paginate(20);
        }

        return User::with('roles')->get();
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
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->input('username'),
            'username' => $request->input('username'),
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
        return User::with('roles')->findOrFail($id);
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
            'name' => 'string|max:255|nullable',
            'password' => 'string|min:8|nullable',
            'roles' => 'array|nullable',
        ]);

        $user = User::findOrFail($id);

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        if ($request->has('password')) {
            $user->name = Hash::make($request->input('password'));
        }

        if ($request->has('roles')) {
            if ($id !== $request->user()->id) {
                Bouncer::sync($user)->roles($request->input('roles'));
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
        if ($id === $request->user()->id) {
            abort(400);
        }

        $user = User::findOrFail($id);

        $user->delete();
    }
}
