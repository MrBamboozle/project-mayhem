<?php

namespace App\Providers;

use App\Enums\TokenAbility;
use App\Http\Clients\NormatimOsmClient;
use App\Models\PersonalAccessToken;
use App\Services\EventService;
use App\Services\TokenGenerate\TokenGeneration;
use App\Services\UrlQuery\UrlQueries\Filters\EventsFilter;
use App\Services\UrlQuery\UrlQueries\Filters\UsersFilter;
use App\Services\UrlQuery\UrlQueries\Sorts\BaseSort;
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
        $this->app->singleton(UsersFilter::class, fn(Application $app) => new UsersFilter());
        $this->app->singleton(EventsFilter::class, fn(Application $app) => new EventsFilter());
        $this->app->singleton(BaseSort::class, fn(Application $app) => new BaseSort());
        $this->app->singleton(UserService::class, fn(Application $app) => new UserService());
        $this->app->singleton(NormatimOsmClient::class, fn(Application $app) => new NormatimOsmClient());
        $this->app->singleton(EventService::class, fn(Application $app) => new EventService(app(NormatimOsmClient::class)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Sanctum::authenticateAccessTokensUsing(function (PersonalAccessToken $accessToken, bool $is_valid):bool {
            if (!$accessToken->can(TokenAbility::ISSUE_ACCESS_TOKEN->value)) {
                return $is_valid;
            }

            return !$accessToken->expires_at?->isPast() ?? false;
        });
    }
}
