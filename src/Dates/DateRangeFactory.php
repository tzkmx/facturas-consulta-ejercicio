<?php

namespace Jefrancomix\ConsultaFacturas\Dates;

class DateRangeFactory
{
    public function buildRangeForYear(int $year): DateRange
    {
        $start = "{$year}-01-01";
        $finish = "{$year}-12-31";

        $range = new DateRange($start, $finish);
        return $range;
    }

    public function buildFromArray(array $init)
    {
        return new DateRange($init['start'], $init['finish']);
    }
}
