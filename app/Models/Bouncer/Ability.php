<?php

namespace App\Models\Bouncer;

class Ability extends \Silber\Bouncer\Database\Ability
{
    public $hidden = [
        'entity_id',
        'entity_type',
        'only_owned',
        'scope',
        'options',
    ];
}
