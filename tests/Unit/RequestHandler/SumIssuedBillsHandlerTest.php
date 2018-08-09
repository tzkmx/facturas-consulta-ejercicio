<?php

namespace Unit\RequestHandler;

use Jefrancomix\ConsultaFacturas\RequestHandler\SumIssuedBillsHandler;
use PHPUnit\Framework\TestCase;

class SumBillsIssuedHandlerTest extends TestCase
{
    public function testSumBillsIssued()
    {
        $noPendingQueriesRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'isComplete' => true,
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
        $handler = new SumIssuedBillsHandler();
        $requestWithSums = $handler->handleRequest($noPendingQueriesRequest);

        $expectedResolvedRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'isComplete' => true,
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

    /**
     * @expectedException \RuntimeException
     */
    public function testRejectSumOfIncompleteRequest()
    {
        $noYearCompleteRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'pendingQueries' => [],
            'isComplete' => false,
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
        $handler = new SumIssuedBillsHandler();
        $requestWithSums = $handler->handleRequest($noYearCompleteRequest);
    }
}
