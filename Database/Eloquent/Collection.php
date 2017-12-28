<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-12-28
 */

namespace fk\utility\Database\Eloquent;

use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function walk(callable $callback, $userData = null)
    {
        array_walk($this->items, $callback, $userData);
        return $this;
    }
}