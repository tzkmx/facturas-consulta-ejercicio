<?php

namespace Unit\Query;

use Jefrancomix\ConsultaFacturas\Query\QueryInterface;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class RequestSpy implements RequestForYearInterface
{
    private $queryReceived;
    public function clientId(): string
    {
    }

    public function isComplete(): bool
    {
    }

    public function getQueries(): array
    {
    }

    public function reportQuery(QueryInterface $query)
    {
        ;
        $this->queryReceived = $query;
    }
    public function getQueryReceived()
    {
        return $this->queryReceived;
    }
}
