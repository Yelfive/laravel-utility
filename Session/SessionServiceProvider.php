<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-08
 */

namespace fk\utility\Session;

use Illuminate\Support\Facades\Session;

class SessionServiceProvider extends \Illuminate\Session\SessionServiceProvider
{

    protected $token = null;

    /**
     * Register the session manager instance.
     *
     * @return void
     */
    protected function registerSessionManager()
    {
        /*
         * 1. register singleton
         * 2. register shutdown to terminate the session
         *      2.1 when access_token is given
         *      2.2 when session started
         * 3. When access token given, retrieve the session
         *      3.1 if session does not exists, close the session? until another open is called? (Try Retrieving?)
         *
         * 
         * 1. When call methods that need open session,
         */
        $this->registerTerminator();

        $this->app->singleton('session', function ($app) {
            $manager = new Manager($app);

            $manager->registerTokenRetriever(function () {
                $this->token = $token = $this->getAccessToken();
                if ($this->token === null) $this->token = '';
                return $token;
            });
            return $manager;
        });
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
        if (Session::isStarted()) Session::save();
    }

    protected function registerTerminator()
    {
        register_shutdown_function([$this, 'terminate']);
    }
}