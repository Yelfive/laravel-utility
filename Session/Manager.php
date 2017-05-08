<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-08
 */

namespace fk\utility\Session;

use Illuminate\Session\EncryptedStore;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;

/**
 * @method \Illuminate\Contracts\Session\Session driver()
 */
class Manager extends SessionManager
{

    protected $id = null;

//    public function getRawId()
//    {
//        return $this->id;
//    }

//    public $isStarted = false;

    public $autoStartCallings = [
        'get', 'exists', 'has', 'all',
        'put', 'push', 'pull', 'flash', 'save',
        'forget', 'remove', 'flush', 'forget', 'migrate',
        'increment', 'decrement'
    ];

    // terminate session
    // first check if the session need terminating
//    public function trySettingId()
//    {
//        $id = $this->id;
//        $driver = $this->driver();
//
//        if (!method_exists($driver, 'isValidId') || $driver->isValidId($id)) {
//            $driver->setId($this->id = $id);
//        }
//    }

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
     * //     * @return null|string Returns token string, null if no token retrieved
     * @return bool Indicates if the token is retrieved
     */
    protected function retrieveToken():bool
    {
        if (!$this->retrieverCalled && is_callable($this->tokenRetriever)) {
            $this->token = call_user_func($this->tokenRetriever);
        }
        $this->retrieverCalled = true;

        return empty($this->token);
    }

//    protected function getId()
//    {
//        if (!is_string($this->id)) $this->id = $this->driver()->getId();
//        return $this->id;
//    }

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
        if (config('session.auto_start', false) && in_array($method, $this->autoStartCallings) && !$driver->isStarted()) {
            if ($this->retrieveToken() && !method_exists($driver, 'isValidId') || $driver->isValidId($this->token)) {
                $driver->setId($this->id = $this->token);
            }
            $driver->start();
        }

        return $driver->$method(...$parameters);
    }

    protected function isValidToken()
    {

    }

//    public $rawId = null;

    /**
     * @param null|mixed|callable $callable
     * If the param is callable, it should return a string indicates the session id
     */
//    public function setRawId($callable = null)
//    {
//        if (is_string($callable)) {
//            $this->rawId = $callable;
//        } else if (is_callable($callable)) {
//            $this->rawId = $callable();
//        } else if (is_null($callable)) {
//            return;
//        }
//        throw new InvalidArgumentException('Parameter 1 should be string or callable: ', __METHOD__);
//    }
//
//    public function hasRawId()
//    {
//        return is_string($this->rawId);
//    }

}