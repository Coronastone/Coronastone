<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Bouncer;
use Illuminate\Http\Request;

class AbilitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:read-abilities');
        $this->middleware('can:create-abilities')->only('store');
        $this->middleware('can:update-abilities')->only('update');
        $this->middleware('can:delete-abilities')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Bouncer::ability();

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

        return Bouncer::ability()->create([
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
        return Bouncer::ability()->findOrFail($id);
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
        ]);

        $ability = Bouncer::ability()->findOrFail($id);

        if (!$ability->built_in && $request->has('name')) {
            $ability->name = $request->input('name');
        }

        if ($request->has('title')) {
            $ability->title = $request->input('title');
        }

        $ability->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ability = Bouncer::ability()->findOrFail($id);

        if ($ability->roles->count() > 0 || $ability->built_in) {
            abort(400);
        }

        $ability->delete();
    }
}
