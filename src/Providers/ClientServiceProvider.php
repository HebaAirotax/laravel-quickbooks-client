<?php

namespace Spinen\QuickBooks\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Spinen\QuickBooks\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Class ClientServiceProvider
 */
class ClientServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = true;

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [Client::class];
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind(Client::class, function (Application $app) {

            if (Session::has('qbo_connection')) {
                $user = Session::get('qbo_connection');
            } else {
                return false;
            }

            $token =
                $user->quickBooksToken ?:
                $user
                ->quickBooksToken()
                ->make();

            return new Client($app->config->get('quickbooks'), $token);
        });

        $this->app->alias(Client::class, 'QuickBooks');
    }
}
