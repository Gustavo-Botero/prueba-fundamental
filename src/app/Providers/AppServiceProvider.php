<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Modules\UserRepository::class
        );

        // Register UseCases
        $this->app->bind(
            \App\UseCases\Contracts\Auth\RegisterInterface::class,
            \App\UseCases\Modules\Auth\RegisterUsecase::class
        );
        $this->app->bind(
            \App\UseCases\Contracts\Auth\LoginInterface::class,
            \App\UseCases\Modules\Auth\LoginUsecase::class
        );

        $this->app->bind(
            \App\UseCases\Contracts\Task\CreateTaskInterface::class,
            \App\UseCases\Modules\Task\CreateTaskUsecases::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
