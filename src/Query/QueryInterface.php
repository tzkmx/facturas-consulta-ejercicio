<?php

namespace Jefrancomix\ConsultaFacturas\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;

interface QueryInterface
{
    public function range(): DateRange;
    public function tries(): int;
    public function result(): int;
    public function status(): QueryStatus;
    public function error(): string;
    public function saveResult(string $result);
}
