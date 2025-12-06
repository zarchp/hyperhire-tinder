<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureDate();
        $this->configureUrl();
    }

    /**
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    /**
     * Configure the application's commands.
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Model::unguard();

        Model::automaticallyEagerLoadRelationships();
    }

    /**
     * Configure the application's date.
     */
    private function configureDate(): void
    {
        Date::use(CarbonImmutable::class);
    }

    /**
     * Configure the application's URL.
     */
    private function configureUrl(): void
    {
        URL::forceScheme('https');
    }
}
