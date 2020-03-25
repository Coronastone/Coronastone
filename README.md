## Cornerstone

Cornerstone is a scaffold based on the Laravel Framework.

### Features

-   Packages
    -   [brexis/laravel-workflow](https://github.com/brexis/laravel-workflow)
    -   [caouecs/laravel-lang](https://github.com/caouecs/laravel-lang)
    -   [guzzlehttp/guzzle](https://github.com/guzzle/guzzle)
    -   [laravel/passport](https://laravel.com/docs/6.x/passport)
    -   [laravel/telescope](https://laravel.com/docs/6.x/telescope)
    -   [predis/predis](https://github.com/nrk/predis)
    -   [silber/bouncer](https://github.com/JosephSilber/bouncer)

### Remarks

#### Passport

Create the encryption keys and sample clients:

```bash
php artisan passport:install
```

#### Workflow

Publish the configuration:

```bash
php artisan vendor:publish --provider="Brexis\LaravelWorkflow\WorkflowServiceProvider"
```

### License

The MIT License

More info see [LICENSE](LICENSE).
