<?php

namespace App\Providers;

use App\Auth\Grants\DynamicCodeGrant;
use App\Auth\Grants\ExternalCodeGrant;
use App\Models\Bouncer\Ability;
use App\Models\Bouncer\Role;
use App\Models\Passport\Client;
use Bouncer;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

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

        // Enable sms grant type
        // app(AuthorizationServer::class)->enableGrantType(
        //     $this->makeDynamicCodeGrant(),
        //     Passport::tokensExpireIn()
        // );

        // Enable socialite grant type
        // app(AuthorizationServer::class)->enableGrantType(
        //     $this->makeExternalCodeGrant(),
        //     Passport::tokensExpireIn()
        // );

        Passport::routes();
        Passport::enableImplicitGrant();
        Passport::useClientModel(Client::class);

        //
    }

    /**
     * Create and configure a Dynamic Code grant instance.
     *
     * @return DynamicCodeGrant
     */
    protected function makeDynamicCodeGrant()
    {
        $grant = new DynamicCodeGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    /**
     * Create and configure a Dynamic Code grant instance.
     *
     * @return ExternalCodeGrant
     */
    protected function makeExternalCodeGrant()
    {
        $grant = new ExternalCodeGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
