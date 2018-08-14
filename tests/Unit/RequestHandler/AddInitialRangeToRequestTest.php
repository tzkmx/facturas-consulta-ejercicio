<?php

namespace Unit\RequestHandler;

use Jefrancomix\ConsultaFacturas\RequestHandler\AddInitialRangeToRequest;
use PHPUnit\Framework\TestCase;

/**
 * @group RequestHandlerRefactor
 */
class AddInitialRangeToRequestTest extends TestCase
{
    public function testRequestIncludesInitialRange()
    {
        $handler = new AddInitialRangeToRequest();
        $previousRequest = [
            'clientId' => 'whatever',
            'year' => '2017',
        ];
        $expectedRequest = [
            'clientId' => 'whatever',
            'year' => '2017',
            'pendingQueries' => [
                [
                    'start' => '2017-01-01',
                    'finish' => '2017-12-31',
                ],
            ],
        ];

        $request = $handler->handle($previousRequest);

        $this->assertEquals($expectedRequest, $request);
    }
}
