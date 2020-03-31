<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Bouncer;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:read-roles');
        $this->middleware('can:create-roles')->only('store');
        $this->middleware('can:update-roles')->only('update');
        $this->middleware('can:delete-roles')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return Bouncer::role()->paginate(20);
        }

        return Bouncer::role()->get();
    }

    /**
     * Display a listing of abilities.
     *
     * @return \Illuminate\Http\Response
     */
    public function abilities()
    {
        return Bouncer::ability()->get();
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
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
        ]);

        Bouncer::role()->create([
            'name' => $request->input('name'),
            'title' => $request->input('title'),
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
        return Bouncer::role()
            ->with('abilities')
            ->findOrFail($id);
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
            'title' => 'string|max:255|nullable',
            'abilities' => 'array|nullable',
        ]);

        $role = Bouncer::role()->findOrFail($id);

        if ($request->has('name')) {
            $role->name = $request->input('name');
        }

        if ($request->has('title')) {
            $role->title = $request->input('title');
        }

        if ($request->has('abilities')) {
            if ($role->name !== 'admin') {
                Bouncer::sync($role)->abilities($request->input('abilities'));
            }
        }

        $role->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Bouncer::role()->findOrFail($id);

        if ($role->roles->count() > 0) {
            abort(400);
        }

        $role->delete();
    }
}
