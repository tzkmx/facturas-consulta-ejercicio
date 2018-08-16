<?php

namespace Jefrancomix\ConsultaFacturas\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class QueryFactory
{
    private $dateRangeFactory;
    public function __construct(DateRangeFactory $factory)
    {
        $this->dateRangeFactory = $factory;
    }

    public function buildInitialQueryFromYear(
        int $year,
        RequestForYearInterface $request
    ) {
        $range = $this->dateRangeFactory->buildRangeForYear($year);
        $query = new Query($range, $request);
        return $query;
    }

    public function buildQueriesSplitting(
        QueryInterface $query,
        RequestForYearInterface $request
    ) {
        $ranges = $this->dateRangeFactory
            ->buildArrayOfRangesSplitting($query->range());

        return array_map(function ($range) use ($request) {
            return new Query($range, $request);
        }, $ranges);
    }
}
