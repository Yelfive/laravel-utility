<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-09
 */

namespace fk\utility\Auth\Session;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class SessionGuardServiceProvider extends ServiceProvider
{

    /**
     * ------------------------------------------------
     * A guard secures the session
     * Provides the entry for user's identity
     * ------------------------------------------------
     * A provider provides the actual data
     * Defines how to retrieve data
     * ------------------------------------------------
     *
     * @see \Illuminate\Auth\AuthManager::resolve()
     */
    public function register()
    {
        // Extends a guard driver
        Auth::extend('easy.token', function ($app, $name, $config) {
            /**
             * @var Application $app
             * @var string $name  Guard name
             * @var array $config e.g. config(auth.$name)
             */
            return new TokenGuard($name, new UserProvider($config), $this->app['session.store'], $this->app['request']);
        });
    }
}