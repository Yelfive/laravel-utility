<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-08
 */

namespace fk\utility\Session;

use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Session;

class SessionServiceProvider extends \Illuminate\Session\SessionServiceProvider
{

    protected $manger;

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
            return $this->manger = $manager;
        });

        $this->registerTerminator();
    }

    /**
     * @return null|string
     */
    public function getAccessToken()
    {
        //
    }

    public function terminate()
    {
        if ($this->manger instanceof SessionManager && Session::isStarted()) Session::save();
    }

    protected function registerTerminator()
    {
        register_shutdown_function([$this, 'terminate']);
    }
}