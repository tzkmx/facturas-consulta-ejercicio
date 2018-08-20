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
    private $daysOfYear;

    private $memoizedCompletionStatus;

    public function __construct(string $clientId, int $year, QueryFactory $factory, string $endpoint)
    {
        $this->clientId = $clientId;
        $this->year = $year;
        $this->endpoint = $endpoint;

        $this->queryFactory = $factory;

        $firstQuery = $this->queryFactory->buildInitialQueryFromYear($year, $clientId);

        $this->daysOfYear = $firstQuery->range()->getDaysInRange();

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
            $validateQueryStatus = $this->validateQueries();

            $invalidQueryFound = $validateQueryStatus['invalidQueryFound'];

            if ($invalidQueryFound) {
                $this->memoizedCompletionStatus = false;
                return false;
            }

            $daysCovered = $validateQueryStatus['daysCovered'];

            if ($this->daysOfYear !== $daysCovered) {
                $this->memoizedCompletionStatus = false;
                return false;
            }

            return true;
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

    public function updateStatus(): bool
    {
        $this->memoizedCompletionStatus = null;

        $this->queries = array_reduce(
            $this->queries,
            function ($accumulator, QueryInterface $query) {
                $queryStatusClass = get_class($query->status());

                if ($queryStatusClass === QueryStatusRangeExceededThreshold::class) {
                    $this->errorQueries[] = $query;

                    $newQueries = $this->queryFactory
                        ->buildQueriesSplitting($query, $this->clientId);

                    array_push($accumulator, ...$newQueries);
                    return $accumulator;
                }

                $accumulator[] = $query;

                return $accumulator;
            },
            []
        );

        return $this->isComplete();
    }

    private function validateQueries()
    {
        $initialValidStatus = [
            'invalidQueryFound' => false,
            'daysCovered' => 0,
        ];

        return array_reduce(
            $this->queries,
            function ($statusSoFar, QueryInterface $query) {
                if ($statusSoFar['invalidQueryFound']) { // ya encontramos consulta incompleta o no válida
                    return ['invalidQueryFound' => true];
                }

                if (!$query->status() instanceof QueryStatusResultOk) {
                    return ['invalidQueryFound' => true];
                }

                $searchIntersection = $this->lookForIntersectionWithOtherQueries($query);

                $daysCoveredSoFar = $statusSoFar['daysCovered'] ?? 0;
                $daysCoveredByThisQuery = $query->range()->getDaysInRange();

                return [
                    'invalidQueryFound' => $searchIntersection,
                    'daysCovered' => $daysCoveredSoFar + $daysCoveredByThisQuery,
                ];
            },
            $initialValidStatus
        );
    }

    private function lookForIntersectionWithOtherQueries(QueryInterface $query)
    {
        return array_reduce(
            $this->queries,
            function ($foundIntersection, QueryInterface $queryToCompare) use ($query) {
                if ($foundIntersection) { // ya encontramos intersección
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
}
