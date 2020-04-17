<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ExternalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send dynamic codes to user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function code(Request $request)
    {
        // $phone_number = $request->input('phone_number');

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
        // } catch (Exception $e) {
        //     throw new Exception('Code generation failed', 0, $e);
        // }

        // TODO: Send code via SMS.

        throw new Exception('Not Implemented.');
    }

    /**
     * Redirect the user to the Socialite authentication page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $provider
     * @return \Illuminate\Http\Response
     */
    public function redirect(Request $request, $provider)
    {
        if ($request->ajax()) {
            return $this->socialite($provider, true)
                ->redirect()
                ->getTargetUrl();
        }

        return $this->socialite($provider)->redirect();
    }

    /**
     * Obtain the user information from Socialite.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\Response
     */
    public function callback($provider)
    {
        $info = $this->socialite($provider)->user();

        $model = new User();

        $user = $model->findByLogin($provider, $info->getId());

        if ($user) {
            Auth::login($user);

            return redirect()->route('home');
        }

        if (!env('EXTERNAL_CREATE_USER', false)) {
            return redirect()->to('/');
        }

        $user = $model->createForLogin($provider, $info->getId());

        Auth::login($user);

        return redirect()->route('home');
    }

    private function socialite($provider, $stateless = false)
    {
        $driver = Socialite::driver($provider);

        if ($stateless) {
            return $driver
                ->redirectUrl(env('EXTERNAL_STATELESS_REDIRECT'))
                ->stateless();
        }

        return $driver->redirectUrl(
            route('auth.callback', [
                'provider' => $provider,
            ])
        );
    }
}
