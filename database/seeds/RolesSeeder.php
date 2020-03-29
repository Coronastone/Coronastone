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
                'name' => 'admin'
            ],
            [
                'title' => __('Administrator')
            ]
        );

        Bouncer::allow('admin')->to('view-dashboard');
        Bouncer::allow('admin')->to('view-telescope');
    }
}
