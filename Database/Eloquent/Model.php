<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-28
 */

namespace fk\utility\Database\Eloquent;

class Model extends \Illuminate\Database\Eloquent\Model
{

    /**
     * @inheritdoc
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}