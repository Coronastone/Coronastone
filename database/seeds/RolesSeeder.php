<?php

use App\Models\Bouncer\Ability;
use App\Models\Bouncer\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bouncer::role()->updateOrCreate(
            [
                'name' => 'admin',
            ],
            [
                'title' => 'Administrator',
            ]
        );

        Bouncer::allow('admin')->to('view-dashboard');
        Bouncer::allow('admin')->to('view-telescope');

        Bouncer::allow('admin')->to('read', Ability::class);
        Bouncer::allow('admin')->to('create', Ability::class);
        Bouncer::allow('admin')->to('update', Ability::class);
        Bouncer::allow('admin')->to('delete', Ability::class);

        Bouncer::allow('admin')->to('read', Role::class);
        Bouncer::allow('admin')->to('create', Role::class);
        Bouncer::allow('admin')->to('update', Role::class);
        Bouncer::allow('admin')->to('delete', Role::class);

        Bouncer::allow('admin')->to('read', User::class);
        Bouncer::allow('admin')->to('create', User::class);
        Bouncer::allow('admin')->to('update', User::class);
        Bouncer::allow('admin')->to('delete', User::class);
    }
}
