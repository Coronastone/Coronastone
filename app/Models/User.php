<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRolesAndAbilities;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['email_verified_at', 'phone_number_verified_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'email',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'email', 'phone_number', 'remember_token'];

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Check the dynamic codes for the user.
     *
     * @param  string  $code
     * @return bool
     */
    // public function checkCodeForPassport($code)
    // {
    //     $key = 'passport_dynamic_code_' . $this->phone_number;

    //     $value = Cache::get($key);

    //     if ($code && $value && $code === $value) {
    //         Cache::forget($key);

    //         return true;
    //     }

    //     return false;
    // }

    /**
     * Generate the dynamic codes for the user.
     *
     * @return string
     */
    // public function generateCodeForPassport()
    // {
    //     try {
    //         if (function_exists('random_int')) {
    //             $code = random_int(100000, 999999);
    //         } else {
    //             $code = mt_rand(100000, 999999);
    //         }

    //         Cache::put(
    //             'passport_dynamic_code_' . $this->phone_number,
    //             $code,
    //             now()->addMinutes(5)
    //         );
    //     } catch (\Exception $e) {
    //         throw new \Exception('Code generation failed', 0, $e);
    //     }

    //     return $code;
    // }
}
