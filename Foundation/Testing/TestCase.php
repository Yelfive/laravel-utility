<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-14
 */

namespace fk\utility\Foundation\Testing;

use fk\utility\Http\{
    Request, SymfonyRequest
};
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Testing\TestCase as TestCaseBase;

abstract class TestCase extends TestCaseBase
{

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $kernel = $this->app->make(HttpKernel::class);

        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest($uri), $method, $parameters,
            $cookies, $files, array_replace($this->serverVariables, $server), $content
        );

        $response = $kernel->handle(
            $request = Request::createFromBase($symfonyRequest)
        );

        $kernel->terminate($request, $response);

        return $this->createTestResponse($response);
    }

    /**
     * Create the test response instance from the given response.
     * @inheritdoc
     */
    protected function createTestResponse($response)
    {
        return TestResponse::fromBaseResponse($response);
    }

    public function get($uri, array $data = [], array $headers = [])
    {
        if ($data) $uri .= (strpos($uri, '?') ? '&' : '?') . http_build_query($data);
        return parent::get($uri, $headers);
    }
}
