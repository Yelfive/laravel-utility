<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-28
 */

namespace fk\utility\Database\Eloquent;

use fk\utility\Database\Query\Builder;

class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new Builder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }
}