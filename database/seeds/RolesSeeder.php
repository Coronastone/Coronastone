<?php

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

        Bouncer::allow('admin')->to('read-roles');
        Bouncer::allow('admin')->to('create-roles');
        Bouncer::allow('admin')->to('update-roles');
        Bouncer::allow('admin')->to('delete-roles');

        Bouncer::allow('admin')->to('read-users');
        Bouncer::allow('admin')->to('create-users');
        Bouncer::allow('admin')->to('update-users');
        Bouncer::allow('admin')->to('delete-users');
    }
}
