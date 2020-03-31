<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('home');
        $this->middleware('auth:api')->only('guard');
    }

    /**
     * Show the welcome.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function home()
    {
        return view('home');
    }

    public function guard()
    {
        //
    }
}
