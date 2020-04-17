<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $fillable = ['user_id', 'provider', 'token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
