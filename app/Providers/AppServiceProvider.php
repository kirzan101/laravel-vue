<?php

namespace App\Providers;

use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use App\Interfaces\FetchInterfaces\UserFetchInterface;
use App\Interfaces\ProfileInterface;
use App\Interfaces\UserInterface;
use App\Services\FetchServices\ProfileFetchService;
use App\Services\FetchServices\UserFetchService;
use App\Services\ProfileService;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserService::class);
        $this->app->bind(UserFetchInterface::class, UserFetchService::class);
        $this->app->bind(ProfileInterface::class, ProfileService::class);
        $this->app->bind(ProfileFetchInterface::class, ProfileFetchService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
