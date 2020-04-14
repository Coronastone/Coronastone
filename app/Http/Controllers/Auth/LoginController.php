<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Send dynamic codes to user.
     *
     * @return void
     */
    public function code(Request $request)
    {
        $phone_number = $request->input('phone_number');

        // try {
        //     if (function_exists('random_int')) {
        //         $code = random_int(100000, 999999);
        //     } else {
        //         $code = mt_rand(100000, 999999);
        //     }

        //     Cache::put(
        //         'passport_dynamic_code_' . $this->phone_number,
        //         $code,
        //         now()->addMinutes(5)
        //     );
        // } catch (\Exception $e) {
        //     throw new \Exception('Code generation failed', 0, $e);
        // }

        // TODO: Send code via SMS.

        throw new \Exception('Not Implemented.');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }
}
