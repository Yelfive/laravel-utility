<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-08
 */

namespace fk\utility\Session;

use Illuminate\Session\EncryptedStore;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Arr;

/**
 * @method \Illuminate\Contracts\Session\Session driver()
 */
class Manager extends SessionManager
{

    protected $id = null;

    public $autoStartCallings = [
        'get', 'exists', 'has', 'all',
        'put', 'push', 'pull', 'flash', 'save',
        'forget', 'remove', 'flush', 'forget', 'migrate',
        'increment', 'decrement'
    ];

    /**
     * @var null|callable
     */
    protected $tokenRetriever = null;

    protected $token = null;

    protected $retrieverCalled = false;

    /**
     * @param callable $retriever
     * $retriever should return token string, null if no token retrieved
     *
     */
    public function registerTokenRetriever(callable $retriever)
    {
        $this->tokenRetriever = $retriever;
    }

    /**
     * Retrieves the token and return if token retrieved
     * @return bool Indicates if the token is retrieved
     */
    protected function retrieveToken(): bool
    {
        /*
         +------------------------------------------------------------------------
         | Session may be set, session id exists, but not started.
         | In which case, session id should not be taken from `retrieveToken`
         | Such as `\Illuminate\Auth\SessionGuard::updateSession`,
         | which stores session, generates session id, but does not start the session.
         | This is an issue of Laravel.
         +------------------------------------------------------------------------
         */
        if ($token = $this->driver()->getId()) {
            $this->token = $token;
        } else if (!$this->retrieverCalled && is_callable($this->tokenRetriever)) {
            $this->token = call_user_func($this->tokenRetriever);
        }
        $this->retrieverCalled = true;

        return empty($this->token);
    }

    /**
     * Build the session instance.
     *
     * @param  \SessionHandlerInterface $handler
     * @return \Illuminate\Session\Store
     */
    protected function buildSession($handler)
    {
        if ($this->app['config']['session.encrypt']) {
            return $this->buildEncryptedSession($handler);
        } else {
            return new Store($this->app['config']['session.cookie'], $handler, $this->id);
        }
    }

    /**
     * Build the encrypted session instance.
     *
     * @param  \SessionHandlerInterface $handler
     * @return \Illuminate\Session\EncryptedStore
     */
    protected function buildEncryptedSession($handler)
    {
        return new EncryptedStore(
            $this->app['config']['session.cookie'], $handler, $this->app['encrypter'], $this->id
        );
    }

    public function __call($method, $parameters)
    {
        $driver = $this->driver();

        // When no `rawId` given, it will have the behavior the Laravel has
        // When `rawId` given
        if (
            config('session.auto_start', false)
            && in_array($method, $this->autoStartCallings)
            && !$driver->isStarted()
        ) {
            if (
                // If token retrieved
                $this->retrieveToken()
                // And if the token is a valid one
                && !method_exists($driver, 'isValidId') || $driver->isValidId($this->token)
            ) {
                $driver->setId($this->id = $this->token);
            }
            $driver->start();

            $this->collectGarbage();
        }

        return $driver->$method(...$parameters);
    }

    /**
     * Remove the garbage from the session if necessary.
     */
    public function collectGarbage()
    {
        $config = $this->getSessionConfig();

        // Here we will see if this request hits the garbage collection lottery by hitting
        // the odds needed to perform garbage collection on any given request. If we do
        // hit it, we'll call this handler to let it delete all the expired sessions.
        if ($this->configHitsLottery($config)) {
            $this->driver()->getHandler()->gc($this->getSessionLifetimeInSeconds());
        }
    }

    /**
     * Determine if the configuration odds hit the lottery.
     *
     * @param  array $config
     * @return bool
     */
    protected function configHitsLottery(array $config)
    {
        return random_int(1, $config['lottery'][1]) <= $config['lottery'][0];
    }

    /**
     * Get the session lifetime in seconds.
     *
     * @return int
     */
    protected function getSessionLifetimeInSeconds()
    {
        return Arr::get($this->getSessionConfig(), 'lifetime') * 60;
    }

}