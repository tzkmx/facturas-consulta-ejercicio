<?php

namespace Jefrancomix\ConsultaFacturas\Request;

use Jefrancomix\ConsultaFacturas\Query\QueryInterface;

class RequestForYearComplete implements RequestForYearCompleteInterface
{
    private $requestForYear;
    private $totalBills;
    private $totalQueries;
    const QUERIES = 'queries';
    const BILLS = 'bills';
    
    public function __construct(RequestForYearInterface $requestForYear)
    {
        $this->requestForYear = $requestForYear;

        $this->processQueries(
            $requestForYear->getQueries(),
            $requestForYear->getErrorQueries()
        );
    }
    private function processQueries(array $queries, array $errorQueries)
    {
        $okTotals = array_reduce(
            $queries,
            function ($accumulator, QueryInterface $query) {
                list(
                    self::QUERIES => $queries,
                    self::BILLS => $bills,
                ) = $accumulator;
                return [
                    self::QUERIES => $queries + $query->tries(),
                    self::BILLS => $bills + $query->result(),
                ];
            },
            [
                self::QUERIES => 0,
                self::BILLS => 0,
            ]
        );
        $this->totalBills = $okTotals[self::BILLS];

        $countErrorQueries = array_sum(array_map(function ($query) {
            return $query->tries();
        }, $errorQueries));

        $this->totalQueries = $okTotals[self::QUERIES] + $countErrorQueries;
    }

    public function totalBills(): int
    {
        return $this->totalBills;
    }

    public function totalQueries(): int
    {
        return $this->totalQueries;
    }

    public function clientId(): string
    {
        return $this->requestForYear->clientId();
    }

    public function isComplete(): bool
    {
        return $this->requestForYear->isComplete();
    }

    public function getQueries(): array
    {
        return $this->requestForYear->getQueries();
    }

    public function getErrorQueries(): array
    {
        return $this->requestForYear->getErrorQueries();
    }

    public function updateStatus(): bool
    {
        return false;
    }
}
