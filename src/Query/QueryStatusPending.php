<?php

namespace Jefrancomix\ConsultaFacturas\Query;

class QueryStatusPending implements QueryStatus
{
    public function value(): int
    {
        return QueryStatus::PENDING;
    }
}
