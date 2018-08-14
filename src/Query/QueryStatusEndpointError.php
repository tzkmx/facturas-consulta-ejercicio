<?php

namespace Jefrancomix\ConsultaFacturas\Query;

class QueryStatusEndpointError implements QueryStatus
{
    public function value(): int
    {
        return QueryStatus::ENDPOINT_ERROR;
    }
}
