<?php

namespace Jefrancomix\ConsultaFacturas\Request;

use Jefrancomix\ConsultaFacturas\Query\QueryInterface;

interface RequestForYearInterface
{
    public function clientId(): string;

    public function isComplete(): bool;

    public function getQueries(): array;

    public function reportQuery(QueryInterface $query);
}
