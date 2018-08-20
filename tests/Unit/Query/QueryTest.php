<?php

namespace Unit\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\Query;
use Jefrancomix\ConsultaFacturas\Query\QueryStatus;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusEndpointError;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusPending;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusRangeExceededThreshold;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusResultOk;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    private $query;
    private $range;
    private $dateRangeFactory;
    public function setUp()
    {
        $this->dateRangeFactory = new DateRangeFactory();
    }

    public function testQueryInitialized()
    {
        $this->givenInitialQuery();

        $this->whenRangeInQueryIs($this->range);

        $this->thenQueryShouldHaveProperties(
            $tries = 0,
            $result = 0,
            $status = new QueryStatusPending(),
            $error = ''
        );
    }

    public function testQueryResultOk()
    {
        $this->givenInitialQuery();

        $this->whenQueryReceivesResult(20);

        $this->thenQueryShouldHaveProperties(
            $tries = 1,
            $result = 20,
            $status = new QueryStatusResultOk(),
            $error = ''
        );
    }

    public function testQueryRangeExceededThreshold()
    {
        $this->givenInitialQuery();

        $this->whenQueryReceivesResult('"Hay más de 100 resultados"');

        $this->thenQueryShouldHaveProperties(
            $tries = 1,
            $result = 0,
            $status = new QueryStatusRangeExceededThreshold(),
            $error = ''
        );
    }

    public function testQueryRangeError()
    {
        $this->givenInitialQuery();
        // TODO: Define error with status codes, right now only test error is saved
        $this->whenQueryReceivesResult('Error: Endpoint error');

        $this->thenQueryShouldHaveProperties(
            $tries = 1,
            $result = 0,
            $status = new QueryStatusEndpointError(),
            $error = 'Error: Endpoint error'
        );
    }

    private function givenInitialQuery()
    {
        $clientId = 'testing';

        $this->range = $this->dateRangeFactory
            ->buildFromStrings('2017-01-01', '2017-12-31');
        $this->query = new Query($this->range, $clientId);
    }
    private function whenRangeInQueryIs(DateRange $rangeExpected)
    {
        $this->assertEquals($rangeExpected, $this->query->range(), "range mismatch");
    }
    private function whenQueryReceivesResult(string $result)
    {
        $this->query->saveResult($result);
    }
    private function thenQueryShouldHaveProperties(
        int $tries,
        int $result,
        QueryStatus $status,
        string $error
    ) {
        $this->assertEquals($tries, $this->query->tries(), "Tries mismatch");
        $this->assertEquals($result, $this->query->result(), "Result mismatch");
        $this->assertEquals($status, $this->query->status(), "Status mismatch");
        $this->assertEquals($error, $this->query->error(), "Error mismatch");
    }
}
