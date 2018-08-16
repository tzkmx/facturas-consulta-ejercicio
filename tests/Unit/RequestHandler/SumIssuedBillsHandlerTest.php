<?php

namespace Unit\RequestHandler;

use Jefrancomix\ConsultaFacturas\RequestHandler\SumIssuedBillsHandler;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYear;
use PHPUnit\Framework\TestCase;

/**
 * @group RequestHandlerRefactor
 */
class SumBillsIssuedHandlerTest extends TestCase
{
    private $request;
    public function testSumBillsIssued()
    {
        $dateRangesFactory = new DateRangeFactory();
        $queryFactory = new QueryFactory($dateRangesFactory);
        $this->request = new RequestForYear('testing', 2017, $queryFactory);
        $queries = $this->request->getQueries();
        $queries[0]->saveResult(99);

        $handler = new SumIssuedBillsHandler();
        $requestWithSums = $handler->handle($this->request);

        $expectedTotal = 99;
        $this->assertEquals($expectedTotal, $requestWithSums->totalBills());
        $expectedQueriesFetched = 1;
        $this->assertEquals($expectedQueriesFetched, $requestWithSums->totalQueries());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRejectSumOfIncompleteRequest()
    {
        $this->markTestIncomplete('TODO: Somehow build a Request incomplete');
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
        $requestWithSums = $handler->handle($noYearCompleteRequest);
    }
}
