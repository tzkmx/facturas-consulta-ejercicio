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

    public function buildInitialQueryFromYear(int $year, string $clientId)
    {
        $range = $this->dateRangeFactory->buildRangeForYear($year);
        $query = new Query($range, $clientId);
        return $query;
    }

    public function buildQueriesSplitting(QueryInterface $query, string $clientId)
    {
        $ranges = $this->dateRangeFactory
            ->buildArrayOfRangesSplitting($query->range());

        return array_map(function ($range) use ($clientId) {
            return new Query($range, $clientId);
        }, $ranges);
    }
}
