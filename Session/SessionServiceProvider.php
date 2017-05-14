<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-08
 */

namespace fk\utility\Session;

use Illuminate\Support\Facades\Session;

class SessionServiceProvider extends \Illuminate\Session\SessionServiceProvider
{

    /**
     * Register the session manager instance.
     *
     * @return void
     */
    protected function registerSessionManager()
    {
        $this->app->singleton('session', function ($app) {
            $manager = new Manager($app);

            $manager->registerTokenRetriever(function () {
                return $this->getAccessToken();
            });
            return $manager;
        });

        $this->app->terminating(function () {
            if (Session::isStarted()) Session::save();
        });

    }

    /**
     * @return null|string
     */
    public function getAccessToken()
    {
        //
    }

}