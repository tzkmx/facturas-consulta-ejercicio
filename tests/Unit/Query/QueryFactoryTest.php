<?php

namespace Unit\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\Query;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;
use PHPUnit\Framework\TestCase;

class QueryFactoryTest extends TestCase
{
    public function testBuildQueryForWholeYear()
    {
        $request = $this->createMock(RequestForYearInterface::class);

        $dateRangeFactory = new DateRangeFactory();
        $queryFactory = new QueryFactory($dateRangeFactory);

        $expectedDateRange = $dateRangeFactory->buildRangeForYear(2017);
        $expectedQuery = new Query($expectedDateRange, $request);

        $query = $queryFactory->buildInitialQueryFromYear(2017, $request);

        $this->assertEquals($expectedQuery, $query);
    }
}
