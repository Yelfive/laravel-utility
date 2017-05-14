<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-14
 */

namespace fk\utility\Foundation\Testing;

use Illuminate\Foundation\Testing\TestResponse as TestResponseBase;

class TestResponse extends TestResponseBase
{

    /**
     * Get the assertion message for assertJson.
     *
     * @param  array $data
     * @return string
     */
    protected function assertJsonMessage(array $data)
    {
        $option = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $expected = json_encode($data, $option);

        $actual = json_encode($this->decodeResponseJson(), $option);

        return 'Unable to find JSON: ' . PHP_EOL . PHP_EOL .
        "[{$expected}]" . PHP_EOL . PHP_EOL .
        'within response JSON:' . PHP_EOL . PHP_EOL .
        "[{$actual}]." . PHP_EOL . PHP_EOL;
    }

}