<?php

namespace App\Providers;

use App\Interfaces\ActivityLogInterface;
use App\Interfaces\FetchInterfaces\ActivityLogFetchInterface;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use App\Interfaces\FetchInterfaces\UserFetchInterface;
use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use App\Interfaces\FetchInterfaces\UserGroupPermissionFetchInterface;
use App\Interfaces\PermissionInterface;
use App\Interfaces\ProfileInterface;
use App\Interfaces\UserGroupInterface;
use App\Interfaces\UserGroupPermissionInterface;
use App\Interfaces\UserInterface;
use App\Services\ActivityLogService;
use App\Services\FetchServices\ActivityLogFetchService;
use App\Services\FetchServices\PermissionFetchService;
use App\Services\FetchServices\ProfileFetchService;
use App\Services\FetchServices\UserFetchService;
use App\Services\FetchServices\UserGroupFetchService;
use App\Services\FetchServices\UserGroupPermissionFetchService;
use App\Services\ProfileService;
use App\Services\UserGroupPermissionService;
use App\Services\UserGroupService;
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
        $this->app->bind(ActivityLogInterface::class, ActivityLogService::class);
        $this->app->bind(ActivityLogFetchInterface::class, ActivityLogFetchService::class);
        $this->app->bind(UserGroupInterface::class, UserGroupService::class);
        $this->app->bind(UserGroupFetchInterface::class, UserGroupFetchService::class);
        $this->app->bind(PermissionInterface::class, PermissionInterface::class);
        $this->app->bind(PermissionFetchInterface::class, PermissionFetchService::class);
        $this->app->bind(UserGroupPermissionInterface::class, UserGroupPermissionService::class);
        $this->app->bind(UserGroupPermissionFetchInterface::class, UserGroupPermissionFetchService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
