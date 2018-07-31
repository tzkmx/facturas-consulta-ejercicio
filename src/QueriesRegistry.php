<?php

namespace Jefrancomix\ConsultaFacturas;

class QueriesRegistry
{
    protected $rangeQueries;

    protected $clientId;

    protected $completed;

    public function __construct(int $year, string $clientId)
    {
        $this->clientId = $clientId;

        $this->completed = false;

        $this->buildInitialRange($year);
    }

    public function getStatus()
    {
        return [
            'completed' => $this->completed,
        ];
    }

    public function getRanges()
    {
        return $this->rangeQueries;
    }

    protected function buildInitialRange(int $year)
    {
        $isLeapYear = ($year % 4 === 0) && (
          ($year % 100 !== 0) || ($year % 400 === 0)
        );

        $start = "{$year}-01-01";

        $howManyDaysHasYear = ($isLeapYear ? 366 : 365) - 1;
        $endDate = \DateTime::createFromFormat('Y z', "{$year} {$howManyDaysHasYear}");

        $finish = $endDate->format('Y-m-d');

        $answer = false;

        $this->rangeQueries = [compact('start', 'finish', 'answer')];
    }
}