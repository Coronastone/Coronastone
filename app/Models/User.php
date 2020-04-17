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
        'email_verified_at',
        'phone_number',
        'phone_number_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'email', 'phone_number', 'remember_token'];

    public function logins()
    {
        return $this->hasMany(Login::class);
    }

    /**
     * Find the user instance for the given login.
     *
     * @param  string  $provider
     * @param  string  $id
     * @return \App\User
     */
    public function findByLogin($provider, $id)
    {
        $login = Login::where('provider', $provider)
            ->where('token', $id)
            ->first();

        if ($login) {
            return $login->user;
        }

        return null;
    }

    /**
     * Create for Login
     *
     * @param  string  $provider
     * @param  string  $id
     * @return \App\User
     */
    public function createForLogin($provider, $id)
    {
        $now = now();
        $username = "$provider{$now->timestamp}$id";

        $user = static::create([
            'name' => $username,
            'username' => $username,
            'password' => bin2hex(openssl_random_pseudo_bytes(13)),
        ]);

        Login::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'token' => $id,
        ]);

        return $user;
    }

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
    //     if (!$this->phone_number_verified_at) {
    //         return false;
    //     }

    //     $key = 'passport_dynamic_code_' . $this->phone_number;

    //     $value = Cache::get($key);

    //     if ($code && $value && $code === $value) {
    //         Cache::forget($key);

    //         return true;
    //     }

    //     return false;
    // }
}
