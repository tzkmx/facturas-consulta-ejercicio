<?php

namespace Jefrancomix\ConsultaFacturas\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;

class QueryFactory
{
    private $dateRangeFactory;
    public function __construct(DateRangeFactory $factory)
    {
        $this->dateRangeFactory = $factory;
    }

    public function buildInitialQueryFromYear(int $year)
    {
        $range = $this->dateRangeFactory->buildRangeForYear($year);
        $query = new Query($range);
        return $query;
    }
}
