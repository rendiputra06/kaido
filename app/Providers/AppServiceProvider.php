<?php

namespace App\Providers;

use App\Interfaces\JadwalKuliahRepositoryInterface;
use App\Interfaces\JadwalServiceInterface;
use App\Interfaces\KelasRepositoryInterface;
use App\Interfaces\RuangKuliahRepositoryInterface;
use App\Models\User;
use App\Repositories\JadwalKuliahRepository;
use App\Repositories\KelasRepository;
use App\Repositories\RuangKuliahRepository;
use App\Services\JadwalService;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));

        // Repositories
        $this->app->bind(KelasRepositoryInterface::class, KelasRepository::class);
        $this->app->bind(JadwalKuliahRepositoryInterface::class, JadwalKuliahRepository::class);
        $this->app->bind(RuangKuliahRepositoryInterface::class, RuangKuliahRepository::class);

        // Services
        $this->app->bind(JadwalServiceInterface::class, JadwalService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::define('viewApiDocs', function (User $user) {
            return true;
        });
        // Gate::policy()
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Google\Provider::class);
        });
    }
}
