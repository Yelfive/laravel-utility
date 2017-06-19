<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-19
 */

namespace fk\utility\Database\Query;

use Illuminate\Database\Query\Builder as QueryBuilder;

class Builder extends QueryBuilder
{
    /**
     * Extend the behavior of select, allow to select with alias like
     * ```
     *  ->select([
     *      'u' => ['id', 'nickname'],
     *      'i' => ['balance', 'intro'],
     *  ])
     * ```
     * For
     * ```
     *  ->select(['u.id', 'u.nickname', 'i.balance', 'i.intro'])
     * ```
     * @inheritdoc
     */
    public function select($columns = ['*'])
    {
        if (is_array($columns) && is_string(key($columns))) {

            $_columns = [];
            foreach ($columns as $alias => $fields) {
                foreach ($fields as $field) {
                    $_columns[] = "$alias.$field";
                }
            }
            $columns = $_columns;
        }

        return parent::select($columns);
    }

}