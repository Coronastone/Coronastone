<?php

namespace App\Models\Bouncer;

class Role extends \Silber\Bouncer\Database\Role
{
    public $hidden = ['level', 'scope'];
}
