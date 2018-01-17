<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-05
 */

namespace fk\utility\Http;

use Illuminate\Support\Facades\Validator;

class Request extends \Illuminate\Http\Request
{

    public static function capture($enableMethodOverride = false)
    {
        if ($enableMethodOverride) static::enableHttpMethodParameterOverride();

        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     * @inheritdoc
     */
    public function expectsJson()
    {
        if (is_bool($this->_expectsJson)) {
            return $this->_expectsJson;
        } else {
            return parent::expectsJson();
        }
    }

    private $_expectsJson = null;

    /**
     * @param callable|bool $expects
     */
    public function setExpectsJson($expects)
    {
        $this->_expectsJson = is_callable($expects) ? call_user_func($expects) : $expects;
    }

    public function validateQuery(array $rules)
    {
        $validator = Validator::make($this->query->all(), $rules);
        return $validator->passes();
    }

    public function validateForm(array $rules)
    {
        $validator = Validator::make($this->request->all(), $rules);
        return $validator->passes();
    }

}
