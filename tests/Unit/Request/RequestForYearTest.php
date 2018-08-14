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
    
    public function setUp()
    {
        $this->dateRangesFactory = new DateRangeFactory();
        $this->queriesFactory = new QueryFactory($this->dateRangesFactory);

        $this->request = new RequestForYear('testing', 2017, $this->queriesFactory);
    }
    public function testGetInitialQuery()
    {
        $expectedRange = new DateRange('2017-01-01', '2017-12-31');
        $expectedQuery = new Query($expectedRange);

        $queries = $this->request->getQueries();

        $this->assertCount(1, $queries);

        $query = $queries[0];

        $this->assertEquals($expectedQuery, $query);

        $this->assertEquals(false, $this->request->isComplete());
    }

    public function testSuccessOfInitialQuery()
    {
        $range = new DateRange('2017-01-01', '2017-12-31');
        $query = new Query($range);

        $query->saveResult(20);

        $this->request->reportQuery($query);

        $this->assertEquals(true, $this->request->isComplete());
    }
}
