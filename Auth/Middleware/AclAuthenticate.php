<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-18
 */

namespace fk\utility\Auth\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AclAuthenticate
{
    protected $auth;

    public function __construct(AuthManager $authManager)
    {
        $this->auth = $authManager;
    }

    public function handle($request, Closure $next)
    {
        if ($this->authenticate()) return $next($request);

        $this->accessDenied();
    }

    /**
     * Called when access is denied
     */
    protected function accessDenied()
    {
        throw new AccessDeniedHttpException('Unauthenticated.');
    }

    /**
     * Define your custom ACL rules here,
     * returns bool to indicates accessible
     * @return bool
     */
    protected function authenticate(): bool
    {
        return true;
    }

}