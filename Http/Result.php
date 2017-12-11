<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-12-08
 */

namespace fk\utility\Http;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class Result extends \fk\helpers\Result implements Responsable
{

    /**
     * @var bool Whether to render code as HTTP Status Code
     */
    protected $codeAsStatus = true;

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function toResponse($request)
    {
        $response = new Response();
        if ($this->codeAsStatus) $response->setStatusCode($this->code);
        return $response->header('Content-Type', 'application/json;charset=utf-8')
            ->setContent($this->toJson());
    }

    public function codeAsStatus($codeAsStatus = true)
    {
        $this->codeAsStatus = $codeAsStatus;
        return $this;
    }
}