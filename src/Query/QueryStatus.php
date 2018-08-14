<?php

namespace Jefrancomix\ConsultaFacturas\Query;

interface QueryStatus
{
    const ENDPOINT_ERROR = -1;
    const PENDING = 0;
    const RESULT_OK = 1;
    const RANGE_EXCEEDED_THRESHOLD = 2;

    public function value(): int;
}
