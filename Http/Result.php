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
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function toResponse($request)
    {
        $response = new Response();
        return $response->setStatusCode($this->code)
            ->header('Content-Type', 'application/json;charset=utf-8')
            ->setContent($this->toJson());
    }
}