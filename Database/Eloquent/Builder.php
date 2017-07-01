<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-29
 */

namespace fk\utility\Database\Eloquent;

use fk\utility\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * @method $this where(string | \Closure | array $column, string $operator = null, $value = null, $boolean = 'and')
 * @method $this orWhere(string | \Closure | array $column, string $operator = null, $value = null, $boolean = 'and')
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{

    /**
     * @inheritdoc
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

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