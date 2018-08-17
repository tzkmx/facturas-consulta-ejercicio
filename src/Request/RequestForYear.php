<?php

namespace Jefrancomix\ConsultaFacturas\Request;

use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryInterface;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusRangeExceededThreshold;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusResultOk;

class RequestForYear implements RequestForYearInterface
{
    protected $clientId;
    protected $year;
    private $endpoint;

    protected $queries;
    protected $queryFactory;

    private $errorQueries;

    private $memoizedCompletionStatus;

    public function __construct(string $clientId, int $year, QueryFactory $factory, string $endpoint)
    {
        $this->clientId = $clientId;
        $this->year = $year;
        $this->endpoint = $endpoint;

        $this->queryFactory = $factory;

        $firstQuery = $this->queryFactory->buildInitialQueryFromYear($year, $this);

        $this->queries = [$firstQuery];
        $this->errorQueries = [];

        $this->memoizedCompletionStatus = null;
    }
    public function clientId(): string
    {
        return $this->clientId;
    }
    public function endpoint(): string
    {
        return $this->endpoint;
    }
    public function isComplete(): bool
    {
        if (is_null($this->memoizedCompletionStatus)) {
            $invalidQueryFound = array_reduce(
                $this->queries,
                [$this, 'aQueryIsInvalid']
            );
            $this->memoizedCompletionStatus = !$invalidQueryFound;
        }

        return $this->memoizedCompletionStatus;
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getErrorQueries(): array
    {
        return $this->errorQueries;
    }

    public function reportQuery(QueryInterface $query)
    {
        $this->memoizedCompletionStatus = null;
        switch (get_class($query->status())) {

            case (QueryStatusRangeExceededThreshold::class):

                $newQueries = $this->queryFactory
                    ->buildQueriesSplitting($query, $this);

                $this->errorQueries[] = $query;

                $this->queries = array_reduce(
                    $this->queries,
                    function ($accumulator, $oldQuery) use ($query) {
                        if ($oldQuery !== $query) {
                            $accumulator[] = $oldQuery;
                        }
                        return $accumulator;
                    },
                    $newQueries
                );

                break;

            default:
                return;
        }
    }

    private function aQueryIsInvalid($init, QueryInterface $query)
    {
        if ($init) { // ya encontramos consulta incompleta o no válida
            return true;
        }
        if (!$query->status() instanceof QueryStatusResultOk) {
            return true;
        }
        return array_reduce(
            $this->queries,
            function ($found, QueryInterface $queryToCompare) use ($query) {
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
            }
        );
    }
}
