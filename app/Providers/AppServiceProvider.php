<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'TransactionsRequestInterface',
            'App\Gnucash\Requests\Sql\SqlTransactionsRequest'
        );

        $this->app->singleton('PeriodService', function() {
            return new \App\Gnucash\Services\PeriodService();
        });

        $this->app->singleton('CommodityService', function() {
            return new \App\Gnucash\Services\CommodityService();
        });

        $this->app->singleton('BalanceService', function() {
            return new \App\Gnucash\Services\BalanceService();
        });

        $this->app->singleton('RepositoryService', function() {
            return new \App\Gnucash\Services\RepositoryService(
                app('App\Gnucash\Backends\BackendInterface')
            );
        });
    }
}
