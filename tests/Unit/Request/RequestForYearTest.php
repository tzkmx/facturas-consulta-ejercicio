<?php

namespace Unit;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\Query;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusRangeExceededThreshold;
use Jefrancomix\ConsultaFacturas\Request\RequestForYear;
use PHPUnit\Framework\TestCase;

class RequestForYearTest extends TestCase
{
    /**
     * @var DateRangeFactory
     */
    protected $dateRangesFactory;
    protected $queriesFactory;
    protected $request;

    public function setUp()
    {
        $this->dateRangesFactory = new DateRangeFactory();
    }
    
    public function testInitialQueryIsNotComplete()
    {
        $this->givenInitialQuery();

        $this->whenInitialQueryStringIsWholeYear();

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
    public function testExceededFirstThenLesserRangesSucceed()
    {
        $this->givenInitialQuery();

        $this->whenWholeYearQueryExceedsThreshold();

        $this->thenRequestHaveProperties(
            $isComplete = false,
            $expectedQueriesLength = 2
        );

        $this->andThenExpectedRangesOfQueriesShouldBe([
            ['2017-01-01', '2017-07-02'],
            ['2017-07-03', '2017-12-31']
        ]);

        $this->whenLesserRangesSucceed();

        $this->thenRequestHaveProperties(
            $isComplete = true,
            $expectedQueriesLength = 2
        );

        $this->andThenErrorQueriesRangesShouldBe([
            ['2017-01-01', '2017-12-31']
        ]);
    }

    private function givenInitialQuery()
    {
        $this->queriesFactory = new QueryFactory($this->dateRangesFactory);

        $this->request = new RequestForYear(
          'testing',
          2017,
          $this->queriesFactory,
          "http://example.com/"
        );
        $this->assertCount(1, $this->request->getQueries());
    }

    private function whenInitialQuerySucceded()
    {
        $queries = $this->request->getQueries();

        $queries[0]->saveResult(20);

        $this->request->updateStatus();
    }

    private function whenInitialQueryStringIsWholeYear()
    {
        $expectedQueryString = 'start=2017-01-01&finish=2017-12-31&id=testing';

        $queries = $this->request->getQueries();

        $this->assertEquals(
            $expectedQueryString,
            $queries[0]->toQueryString(),
            "Initial Query mismatch"
        );
    }

    private function whenWholeYearQueryExceedsThreshold()
    {
        $queries = $this->request->getQueries();
        $queries[0]->saveResult('"Hay más de 100 resultados"');

        $this->request->updateStatus();
    }

    private function whenLesserRangesSucceed()
    {
        $queries = $this->request->getQueries();
        $queries[0]->saveResult(90);
        $queries[1]->saveResult(90);

        $this->request->updateStatus();
    }
    
    private function thenRequestHaveProperties(
        bool $isComplete,
        int $expectedQueriesLength
    ) {
        $this->assertEquals(
            $isComplete,
            $this->request->isComplete(),
            'completion status does not match'
        );

        $queries = $this->request->getQueries();

        $this->assertCount($expectedQueriesLength, $queries, 'queries queue length not matches');
    }
    private function andThenExpectedRangesOfQueriesShouldBe(array $ranges)
    {
        $queries = $this->request->getQueries();

        foreach ($ranges as $index => $range) {
            $this->assertEquals(
                $range,
                array_values($queries[$index]->range()->toArray()),
                "Query {$index} not matches"
            );
        }
    }

    private function andThenErrorQueriesRangesShouldBe(array $ranges)
    {
        $queries = $this->request->getErrorQueries();

        foreach ($ranges as $index => $range) {
            $this->assertEquals(
                $range,
                array_values($queries[$index]->range()->toArray()),
                "Query {$index} not matches"
            );
        }
    }
}
