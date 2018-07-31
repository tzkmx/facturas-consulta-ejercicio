<?php

namespace Jefrancomix\ConsultaFacturas;

class QueriesRegistry
{
    protected $rangeQueries;

    protected $clientId;

    protected $completed;

    protected $year;

    public function __construct(int $year, string $clientId)
    {
        $this->clientId = $clientId;

        $this->completed = false;

        $this->buildInitialRange($year);

        $this->year = $year;
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

    public function enterRangeQueryResult($result)
    {
        $answer = $result['answer'];
        if ($answer === 'excess') {
            $this->exceededAnswerInRangeThenBuildNewQueryRanges($result);
        }
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

    protected function exceededAnswerInRangeThenBuildNewQueryRanges($rangeResult)
    {
        $exceededStart = \DateTime::createFromFormat('Y-m-d', $rangeResult['start']);
        $exceededFinish = \DateTime::createFromFormat('Y-m-d', $rangeResult['finish']);

        $dayNumberStart = $exceededStart->format('z');
        $dayNumberFinish = $exceededFinish->format('z');

        $diff = $dayNumberFinish - $dayNumberStart;

        $halfDiff = (($diff % 2) === 0) ? ($diff / 2) : (intdiv($diff, 2) + 1);

        $dayNumberFirstHalfFinish = $dayNumberStart + $halfDiff;
        $dayNumberLastHalfStart = $dayNumberFirstHalfFinish + 1;

        $newRangeFirstHalfFinish = \DateTime::createFromFormat('Y z', "{$this->year} {$dayNumberFirstHalfFinish}");
        $newRangeLastHalfStart = \DateTime::createFromFormat('Y z', "{$this->year} {$dayNumberLastHalfStart}");

        $newRangeFirstHalf = [
            'start' => $rangeResult['start'],
            'finish' => $newRangeFirstHalfFinish->format('Y-m-d'),
            'answer' => false,
        ];
        $newRangeLastHalf = [
            'start' => $newRangeLastHalfStart->format('Y-m-d'),
            'finish' => $rangeResult['finish'],
            'answer' => false,
        ];

        $newRangesWithoutExceeded = array_filter($this->rangeQueries, function($range) use ($rangeResult) {
            return $range['start'] !== $rangeResult['start'] && $range['finish'] !== $rangeResult['finish'];
        });

        array_push($newRangesWithoutExceeded, $newRangeFirstHalf, $newRangeLastHalf);
        
        $this->rangeQueries = $newRangesWithoutExceeded;
    }
}