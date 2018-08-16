<?php

namespace Jefrancomix\ConsultaFacturas\Request;

interface RequestForYearCompleteInterface extends RequestForYearInterface
{
    public function totalBills(): int;

    public function totalQueries(): int;
}
