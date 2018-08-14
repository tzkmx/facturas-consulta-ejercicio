<?php

namespace Jefrancomix\ConsultaFacturas\Request;

use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryInterface;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusResultOk;

class RequestForYear implements RequestForYearInterface
{
    protected $clientId;
    protected $year;

    protected $queries;
    protected $queryFactory;

    public function __construct(string $clientId, int $year, QueryFactory $factory)
    {
        $this->clientId = $clientId;
        $this->year = $year;
        $this->queryFactory = $factory;

        $firstQuery = $this->queryFactory->buildInitialQueryFromYear($year);

        $this->queries = [$firstQuery];
    }

    public function isComplete(): bool
    {
        $invalidQueryFound = array_reduce(
            $this->queries,
            [$this, 'aQueryIsInvalid'],
            false
        );
        return !$invalidQueryFound;
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function reportQuery(QueryInterface $query)
    {
        // TODO: Implement reportQuery() method.
    }

    private function aQueryIsInvalid(bool $init, QueryInterface $query)
    {
        if ($init) { // ya encontramos consulta incompleta o no válida
            return true;
        }
        if (!$query->status() instanceof QueryStatusResultOk) {
            return true;
        }
        return array_reduce(
            $this->queries,
            function (bool $found, QueryInterface $queryToCompare) use ($query) {
                if ($found) {
                    return true;
                }
                $range = $query->range();
                $rangeToCompare = $queryToCompare->range();
                if ($range === $rangeToCompare) {
                    return false;
                }
                if ($range->intersects($rangeToCompare)) {
                    return true;
                }
            },
            false
        );
    }

    private function hasOverlapQueries(bool $found, QueryInterface $query)
    {
    }
}
