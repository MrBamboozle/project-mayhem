<?php

namespace App\Providers;

use App\Http\Clients\NormatimOsmClient;
use App\Models\PersonalAccessToken;
use App\Services\EventService;
use App\Services\ModelService;
use App\Services\TokenGenerate\TokenGeneration;
use App\Services\UrlQuery\UrlQueryService;
use App\Services\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TokenGeneration::class, fn(Application $app) => new TokenGeneration());
        $this->app->singleton(ModelService::class, fn(Application $app) => new ModelService());
        $this->app->singleton(UserService::class, fn(Application $app) => new UserService());
        $this->app->singleton(UrlQueryService::class, fn(Application $app) => new UrlQueryService());
        $this->app->singleton(EventService::class, fn(Application $app) => new EventService(new NormatimOsmClient()));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
