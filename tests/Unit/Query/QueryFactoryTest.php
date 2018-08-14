<?php

namespace Unit\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\Query;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use PHPUnit\Framework\TestCase;

class QueryFactoryTest extends TestCase
{
    public function testBuildQueryForWholeYear()
    {
        $dateRangeFactory = new DateRangeFactory();
        $queryFactory = new QueryFactory($dateRangeFactory);

        $expectedDateRange = $dateRangeFactory->buildRangeForYear(2017);
        $expectedQuery = new Query($expectedDateRange);

        $query = $queryFactory->buildInitialQueryFromYear(2017);

        $this->assertEquals($expectedQuery, $query);
    }
}
