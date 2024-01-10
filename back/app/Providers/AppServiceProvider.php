<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Services\ModelService;
use App\Services\TokenGenerateService\TokenGeneration;
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
        $this->app->singleton(TokenGeneration::class, function (Application $app) {
            return new TokenGeneration();
        });
        $this->app->singleton(ModelService::class, function (Application $app) {
            return new ModelService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
