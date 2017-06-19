<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-19
 */

namespace fk\utility\Pagination;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class LengthAwarePaginator extends Paginator
{
    public function toArray(array $fields = null)
    {
        $defaultFields = [
            'total', 'per_page', 'current_page', 'last_page',
            'next_page_url', 'prev_page_url', 'from', 'to', 'data'
        ];
        $fields = is_array($fields) ? array_intersect($fields, $defaultFields) : $defaultFields;

        $methods = [
            'total' => 'total',
            'per_page' => 'perPage',
            'current_page' => 'currentPage',
            'last_page' => 'lastPage',
            'next_page_url' => 'nextPageUrl',
            'prev_page_url' => 'previousPageUrl',
            'from' => 'firstItem',
            'to' => 'lastItem',
        ];

        $data = [];
        foreach ($fields as $field) {
            if ($field === 'data') {
                $data[$field] = $this->items->toArray();
            } else {
                $data[$field] = $this->{$methods[$field]}();
            }
        }

        return $data;
    }

    public function toFKStyle()
    {
        $pagination = $this->toArray(['total', 'per_page', 'current_page', 'last_page', 'from', 'to']);

        $list = $this->items->toArray();
        return [
            'list' => $list,
            'pagination' => $pagination,
        ];
    }
}