<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-09
 */

namespace fk\utility\Auth\Session;

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
     * @see Illuminate\Auth\AuthManager::resolve()
     */
    public function register()
    {
        // Extends a guard driver
        // - $app Application
        // - $name string Guard name, e.g. api
        // - $config array auth.$name
        Auth::extend('easy.token', function ($app, $name, $config) {
            return new TokenGuard($name, new UserProvider($config), $this->app['session.store'], $this->app['request']);
        });

    }
    // TODO: Allow to define own rule for retrieving session id at SessionGuardServiceProvider
    // TODO: exception handler, return json all the time, see result when `TokenGuard::check` returns false
}