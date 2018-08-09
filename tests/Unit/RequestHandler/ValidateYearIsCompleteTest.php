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

    /**
     * @dataProvider getBadRangesToMarkIncomplete
     */
    public function testSuccessQueriesDontCoverWholeYear(array $queries)
    {
        $noPendingQueriesButBadRange = [
            'clientId' => 'testing',
            'year' => '2017',
            'pendingQueries' => [],
            'queriesFetched' => count($queries),
            'successQueries' => $queries,
        ];
        $validator = new ValidateYearIsComplete();

        $expectedIncompleteRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'isComplete' => false,
            'pendingQueries' => [],
            'queriesFetched' => count($queries),
            'successQueries' => $queries,
        ];

        $validatedRequest = $validator->handleRequest($noPendingQueriesButBadRange);

        $this->assertEquals($expectedIncompleteRequest, $validatedRequest);
    }

    public function getBadRangesToMarkIncomplete()
    {
        return [
            '1 query, 364 days' => [
                [
                    [
                        'range' => [ 'start' => '2017-01-01', 'finish' => '2017-12-30' ],
                        'tries' => 1,
                        'billsIssued' => 99,
                    ]
                ]
            ],
            '2 queries, 364 days' => [
                [
                    [
                        'range' => [ 'start' => '2017-01-01', 'finish' => '2017-01-31' ],
                        'tries' => 1,
                        'billsIssued' => 10,
                    ],
                    [
                        'range' => [ 'start' => '2017-02-01', 'finish' => '2017-12-30' ],
                        'tries' => 1,
                        'billsIssued' => 20,
                    ]
                ]
            ]
        ];
    }
}
