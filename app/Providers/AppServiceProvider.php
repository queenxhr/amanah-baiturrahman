<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\RepositoryInterfaces\Wakif\WakifAuthRepositoryInterface::class,
            \App\Repositories\Wakif\WakifAuthRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Wakif\WakifDashboardRepositoryInterface::class,
            \App\Repositories\Wakif\WakifDashboardRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Wakif\WakifTransaksiRepositoryInterface::class,
            \App\Repositories\Wakif\WakifTransaksiRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Wakif\WakifUserRepositoryInterface::class,
            \App\Repositories\Wakif\WakifUserRepository::class
        );

        $this->app->bind(
            \App\RepositoryInterfaces\Nazhir\NazhirAuthRepositoryInterface::class,
            \App\Repositories\Nazhir\NazhirAuthRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Nazhir\NazhirDashboardRepositoryInterface::class,
            \App\Repositories\Nazhir\NazhirDashboardRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Nazhir\NazhirTransaksiRepositoryInterface::class,
            \App\Repositories\Nazhir\NazhirTransaksiRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Nazhir\NazhirProgramRepositoryInterface::class,
            \App\Repositories\Nazhir\NazhirProgramRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Nazhir\NazhirLaporanRepositoryInterface::class,
            \App\Repositories\Nazhir\NazhirLaporanRepository::class
        );

        // Superadmin bindings
        $this->app->bind(
            \App\RepositoryInterfaces\Superadmin\SuperadminUserRepositoryInterface::class,
            \App\Repositories\Superadmin\SuperadminUserRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Superadmin\SuperadminProgramRepositoryInterface::class,
            \App\Repositories\Superadmin\SuperadminProgramRepository::class
        );
        $this->app->bind(
            \App\RepositoryInterfaces\Superadmin\SuperadminPencairanRepositoryInterface::class,
            \App\Repositories\Superadmin\SuperadminPencairanRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
