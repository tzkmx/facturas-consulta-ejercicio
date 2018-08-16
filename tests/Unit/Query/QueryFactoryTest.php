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
    public function testBuildQueriesSplittingProvidedOne()
    {
        $request = $this->createMock(RequestForYearInterface::class);

        $dateRangeFactory = new DateRangeFactory();

        $originalRange = $dateRangeFactory
            ->buildFromStrings('2018-01-01', '2018-12-31');
        $originalQuery = new Query($originalRange, $request);

        $queryFactory = new QueryFactory($dateRangeFactory);

        $expectedQueries = [];

        $expectedRange1 = $dateRangeFactory
            ->buildFromStrings('2018-01-01', '2018-07-02');
        $expectedQueries[] = new Query($expectedRange1, $request);

        $expectedRange2 = $dateRangeFactory
            ->buildFromStrings('2018-07-03', '2018-12-31');
        $expectedQueries[] = new Query($expectedRange2, $request);

        $queriesBuilt = $queryFactory
            ->buildQueriesSplitting($originalQuery, $request);

        $this->assertEquals($expectedQueries, $queriesBuilt);
    }
}
