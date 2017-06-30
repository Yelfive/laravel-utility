<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-05
 */

namespace fk\utility\Http;

use fk\http\RestfulParser;
use Illuminate\Http\UploadedFile;
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
            $_files = $_FILES;
            // Custom UploadedFile to fool the isValid check
            $_FILES = array_map(function ($file) {
                return new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error'], true);
            }, $_FILES);
        }
        $request = parent::createFromGlobals();
        if ($needParse) $_POST = $_post;
        if (isset($_files)) $_FILES = $_files;

        return $request;
    }

}
