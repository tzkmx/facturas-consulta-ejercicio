<?php

namespace Unit\RequestHandler;

use Jefrancomix\ConsultaFacturas\RequestHandler\SumBillsIssuedHandler;
use PHPUnit\Framework\TestCase;


class SumBillsIssuedHandlerTest extends TestCase
{
    public function testSumBillsIssued()
    {
        $noPendingQueriesRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'pendingQueries' => [],
            'queriesFetched' => 1,
            'successQueries' => [
                [
                    'range' => [
                        'start' => '2017-01-01',
                        'finish' => '2017-12-31',
                    ],
                    'tries' => 1,
                    'billsIssued' => 99,
                ],
            ],
        ];
        $handler = new SumBillsIssuedHandler();
        $requestWithSums = $handler->handleRequest($noPendingQueriesRequest);

        $expectedResolvedRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'billsIssued' => 99,
            'queriesFetched' => 1,
            'successQueries' => [
                [
                    'range' => [
                        'start' => '2017-01-01',
                        'finish' => '2017-12-31',
                    ],
                    'tries' => 1,
                    'billsIssued' => 99,
                ],
            ],
        ];

        $this->assertEquals($expectedResolvedRequest, $requestWithSums);
    }

}
