<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-05
 */

namespace fk\utility\Http;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class Request extends \Illuminate\Http\Request
{

    public static function capture($enableMethodOverride = true)
    {
        if ($enableMethodOverride) static::enableHttpMethodParameterOverride();

        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     * @inheritdoc
     */
    public function expectsJson()
    {
        if ($this->customExpectsJson()) {
            return true;
        } else {
            return parent::expectsJson();
        }
    }

    /**
     * Overwrite it if you have custom expects json
     * @return bool
     * - false to go on the Laravel expects json check
     * - true to indicates expects json
     */
    protected function customExpectsJson(): bool
    {
        return strncmp('/' . Route::current()->uri, '/api/', 5) === 0;
    }

}
