## Coronastone

Coronastone is a scaffold based on the Laravel Framework.

### Features

-   Login / Register
-   Admin Dashboard
    -   Users management
    -   Roles management
-   Packages
    -   [brexis/laravel-workflow](https://github.com/brexis/laravel-workflow/tree/1.3.3)
    -   [caouecs/laravel-lang](https://github.com/caouecs/laravel-lang/tree/5.0.0)
    -   [guzzlehttp/guzzle](http://docs.guzzlephp.org/en/6.5/overview.html)
    -   [laravel/passport](https://laravel.com/docs/6.x/passport)
    -   [laravel/socialite](https://laravel.com/docs/6.x/socialite)
    -   [laravel/telescope](https://laravel.com/docs/6.x/telescope)
    -   [predis/predis](https://github.com/nrk/predis/tree/v1.1)
    -   [silber/bouncer](https://github.com/JosephSilber/bouncer/tree/v1.0.0-rc.7)

### Remarks

#### Bouncer

Seed the default roles:

```bash
$ php artisan db:seed
```

#### Passport

Create the encryption keys and sample clients:

```bash
$ php artisan passport:install
```

To enable `sms` grant type, uncomment codes in `AuthServiceProvider` and `User` model.

#### Socialite

For more providers, see [Socialite Providers](https://socialiteproviders.netlify.com).

#### Telescope

On local (dev) environment, `laravel/telescope` should be installed via `composer install` or `composer update`.

On remote (production) environment, `laravel/telescope` should be ignored via `composer install --no-dev` or `composer update --no-dev`.

#### Workflow

Publish the configuration:

```bash
$ php artisan vendor:publish --provider="Brexis\LaravelWorkflow\WorkflowServiceProvider"
```

### License

The MIT License

More info see [LICENSE](LICENSE).
