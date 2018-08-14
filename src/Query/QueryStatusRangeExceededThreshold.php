<?php

namespace Jefrancomix\ConsultaFacturas\Query;

class QueryStatusRangeExceededThreshold implements QueryStatus
{
    public function value(): int
    {
        return QueryStatus::RANGE_EXCEEDED_THRESHOLD;
    }
}
