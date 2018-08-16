<?php

namespace Unit;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\Query;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYear;
use PHPUnit\Framework\TestCase;

class RequestForYearTest extends TestCase
{
    protected $dateRangesFactory;
    protected $queriesFactory;
    protected $request;
    
    public function testInitialQueryIsNotComplete()
    {
        $this->givenInitialQuery();

        $this->whenInitialQueryIsWholeYear();

        $this->thenRequestHaveProperties(
            $isComplete = false,
            $expectedQueriesLength = 1
        );
    }
    
    public function testSuccessOfInitialQuery()
    {
        $this->givenInitialQuery();

        $this->whenInitialQuerySucceded();

        $this->thenRequestHaveProperties(
            $isComplete = true,
            $expectedQueriesLength = 1
        );
    }

    private function givenInitialQuery()
    {
        $this->dateRangesFactory = new DateRangeFactory();
        $this->queriesFactory = new QueryFactory($this->dateRangesFactory);

        $this->request = new RequestForYear('testing', 2017, $this->queriesFactory);
    }

    private function whenInitialQuerySucceded()
    {
        $queries = $this->request->getQueries();

        $queries[0]->saveResult(20);
    }

    private function whenInitialQueryIsWholeYear()
    {
        $expectedRange = new DateRange('2017-01-01', '2017-12-31');
        $expectedQuery = new Query($expectedRange);

        $queries = $this->request->getQueries();

        $this->assertEquals($expectedQuery, $queries[0], "Initial Query mismatch");
    }
    
    private function thenRequestHaveProperties(
        bool $isComplete,
        int $expectedQueriesLength,
        array $expectedQueries = array()
    ) {
        $this->assertEquals(
            $isComplete,
            $this->request->isComplete(),
            'completion status does not match'
        );

        $queries = $this->request->getQueries();

        $this->assertCount($expectedQueriesLength, $queries, 'queries queue length not matches');

        foreach ($expectedQueries as $index => $expectedQuery) {
            $this->assertEquals($expectedQuery, $queries[$index], "Query {$index} not matches");
        }
    }
}
