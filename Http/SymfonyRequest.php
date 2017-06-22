<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-05
 */

namespace fk\utility\Http;

use fk\http\RestfulParser;
use Symfony\Component\HttpFoundation\Request as RequestBase;

class SymfonyRequest extends RequestBase
{
    public static function createFromGlobals()
    {
        $needParse = RestfulParser::needParse();
        /** @var array $_post */
        if ($needParse) {
            $_post = $_POST;
            $_POST = RestfulParser::parseFormData();
        }
        $request = parent::createFromGlobals();
        if ($needParse) $_POST = $_post;

        return $request;
    }

}
