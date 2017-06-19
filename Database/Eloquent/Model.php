<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-28
 */

namespace fk\utility\Database\Eloquent;

use DateTimeInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class Model extends \Illuminate\Database\Eloquent\Model
{

    /**
     * @var bool
     * @see dateAsInteger()
     */
    public static $serializeDateAsInteger = false;

    /**
     * @var null|MessageBag
     */
    public $errors = null;

    public function __construct(array $attributes = [])
    {
        $this->fillable(array_keys($this->_getRules()));

        parent::__construct($attributes);
    }

    private $_rules;

    private final function _getRules()
    {
        if (is_array($this->_rules)) {
            return $this->_rules;
        }
        $this->_rules = $this->rules();

        if (!is_array($this->_rules)) $this->_rules = [];

        return $this->_rules;
    }

    public function rules()
    {
        return [];
    }

    public function validate(array $attributes = null)
    {
        $validator = Validator::make(is_array($attributes) ? $attributes : $this->attributes, $this->_rules);
        if ($validator->passes()) {
            return true;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    }

    public function getAttributes(array $accept = null, array $except = null)
    {
        $attributes = $this->attributes;
        if ($accept && is_array($accept)) {
            $attributes = array_intersect_key($attributes, array_flip($accept));
        }

        if ($except && is_array($except)) {
            $attributes = array_diff_key($attributes, array_flip($except));
        }

        $attributes = $this->addDateAttributesToArray($attributes);
        return $attributes;
    }

    /**
     * Make it public
     * @inheritdoc
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        return parent::increment($column, $amount, $extra);
    }

    /**
     * Make it public
     * @inheritdoc
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        return parent::decrement($column, $amount, $extra);
    }

    public function hasErrors()
    {
        return isset($this->errors);
    }

    /**
     * @inheritdoc
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Calling this method will cause all
     *  - $this->dates
     *  - static::CREATED_AT
     *  - static::UPDATED_AT
     * to be serialized as integer timestamp
     * @see \Illuminate\Database\Eloquent\Model::getDates
     * @see $dateAsInteger
     *
     * @param bool $asInteger
     * @return $this
     */
    public function dateAsInteger(bool $asInteger = true)
    {
        static::$serializeDateAsInteger = $asInteger;
        return $this;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        if (static::$serializeDateAsInteger) {
            return $date->getTimestamp();
        } else {
            return parent::serializeDate($date);
        }
    }
}