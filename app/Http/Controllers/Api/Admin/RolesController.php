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
        $model = Bouncer::role();

        if ($request->user()->can('read-abilities')) {
            $model = $model->with('abilities');
        }

        $q = $request->input('q');
        if ($q) {
            $model = $model
                ->where('name', 'like', "%$q%")
                ->orWhere('title', 'like', "%$q%");
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
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
        ]);

        return Bouncer::role()->create([
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
        $model = Bouncer::role();

        if ($request->user()->can('read-abilities')) {
            $model = $model->with('abilities');
        }

        return $model->findOrFail($id);
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
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'abilities' => 'nullable|array',
        ]);

        $role = Bouncer::role()->findOrFail($id);

        if ($request->has('name')) {
            $role->name = $request->input('name');
        }

        if ($request->has('title')) {
            $role->title = $request->input('title');
        }

        if (
            $request->user()->can('read-abilities') &&
            $request->has('abilities')
        ) {
            $abilities = collect($request->input('abilities'));

            if ($role->name !== 'admin') {
                Bouncer::sync($role)->abilities(
                    $abilities->map(function ($ability) {
                        return $ability['name'];
                    })
                );
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

        if ($role->users->count() > 0) {
            abort(400);
        }

        $role->delete();
    }
}
