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

        $this->processQueries($requestForYear->getQueries());
    }
    private function processQueries(array $queries)
    {
        $totals = array_reduce(
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
        $this->totalBills = $totals[self::BILLS];
        $this->totalQueries = $totals[self::QUERIES];
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

    public function reportQuery(QueryInterface $query)
    {
        throw new \LogicException('Query Complete, shouldn\'t receive more reports');
    }
}
