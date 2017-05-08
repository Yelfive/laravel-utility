<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-29
 */

namespace fk\utility\Database\Eloquent;

class Builder extends \Illuminate\Database\Eloquent\Builder
{

    public function rawSql(): string
    {
        $sql = $this->toSql();
        $bindings = $this->getBindings();
        $sqlArray = explode('?', $sql);
        $rawSql = array_shift($sqlArray);
        foreach ($bindings as $value) {
            if (!$sqlArray) break;
            $rawSql .= (is_int($value) ? $value : "'$value'") . array_shift($sqlArray);
        }
        return $rawSql;
    }

    public function __toString()
    {
        return $this->rawSql();
    }
}