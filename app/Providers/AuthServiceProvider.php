<?php

namespace App\Providers;

use App\Models\Bouncer\Ability;
use App\Models\Bouncer\Role;
use App\Models\Passport\Client;
use Bouncer;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Bouncer::useAbilityModel(Ability::class);
        Bouncer::useRoleModel(Role::class);

        Passport::routes();
        Passport::enableImplicitGrant();
        Passport::useClientModel(Client::class);

        //
    }
}
