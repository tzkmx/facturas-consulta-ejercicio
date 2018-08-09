<?php

namespace Unit\RequestHandler;

use Jefrancomix\ConsultaFacturas\RequestHandler\ValidateYearIsComplete;
use PHPUnit\Framework\TestCase;

class ValidateYearIsCompleteTest extends TestCase
{
    public function testSuccessQueriesCoverWholeYear()
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
        $validator = new ValidateYearIsComplete();

        $expectedValidRequest = [
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

        $validatedRequest = $validator->handleRequest($noPendingQueriesRequest);

        $this->assertEquals($expectedValidRequest, $validatedRequest);
    }

    public function testSuccessQueriesDontCoverWholeYear()
    {
        $noPendingQueriesButBadRange = [
            'clientId' => 'testing',
            'year' => '2017',
            'pendingQueries' => [],
            'queriesFetched' => 1,
            'successQueries' => [
                [
                    'range' => [
                        'start' => '2017-01-01',
                        'finish' => '2017-12-30',
                    ],
                    'tries' => 1,
                    'billsIssued' => 99,
                ],
            ],
        ];
        $validator = new ValidateYearIsComplete();

        $expectedIncompleteRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'isComplete' => false,
            'pendingQueries' => [],
            'queriesFetched' => 1,
            'successQueries' => [
                [
                    'range' => [
                        'start' => '2017-01-01',
                        'finish' => '2017-12-30',
                    ],
                    'tries' => 1,
                    'billsIssued' => 99,
                ],
            ],
        ];

        $validatedRequest = $validator->handleRequest($noPendingQueriesButBadRange);

        $this->assertEquals($expectedIncompleteRequest, $validatedRequest);
    }
}
