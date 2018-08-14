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
    public function testGetInitialQuery()
    {
        $dateRangesFactory = new DateRangeFactory();
        $queriesFactory = new QueryFactory($dateRangesFactory);

        $request = new RequestForYear('testing', 2017, $queriesFactory);

        $expectedRange = new DateRange('2017-01-01', '2017-12-31');
        $expectedQuery = new Query($expectedRange);

        $queries = $request->getQueries();

        $this->assertCount(1, $queries);

        $query = $queries[0];

        $this->assertEquals($expectedQuery, $query);

        $this->assertEquals(false, $request->isComplete());
    }
}
