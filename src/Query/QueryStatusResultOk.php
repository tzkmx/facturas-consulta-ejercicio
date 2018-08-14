<?php

namespace Jefrancomix\ConsultaFacturas\Query;

class QueryStatusResultOk implements QueryStatus
{
    public function value(): int
    {
        return QueryStatus::RESULT_OK;
    }
}
