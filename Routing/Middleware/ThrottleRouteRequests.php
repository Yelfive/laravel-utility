<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-12-20
 */

namespace fk\utility\Routing\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\App;

/**
 * Throttle for per route
 */
class ThrottleRouteRequests extends ThrottleRequests
{

    protected static $ignore = false;

    /**
     * Whether to ignore this throttle check
     * @param bool $ignore
     */
    public static function ignore(bool $ignore = true)
    {
        static::$ignore = $ignore;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  int|string $maxAttempts
     * @param  float|int $decayMinutes
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            throw $this->buildException($key, $maxAttempts);
        }

        $response = $next($request);

        App::terminating(function () use ($key, $decayMinutes) {
            if (!static::$ignore) $this->limiter->hit($key, $decayMinutes);
        });

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature($request)
    {
        return sha1(parent::resolveRequestSignature($request) . '_' . $request->route()->uri());
    }
}