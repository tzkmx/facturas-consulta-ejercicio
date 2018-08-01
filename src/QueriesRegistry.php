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
        $this->year = $year;

        $isLeapYear = ($year % 4 === 0) && (
          ($year % 100 !== 0) || ($year % 400 === 0)
        );

        $start = "{$year}-01-01";

        $howManyDaysHasYear = ($isLeapYear ? 366 : 365) - 1;

        $finish = $this->getIsoDateForDayOfYear($howManyDaysHasYear);

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

        $newRangeFirstHalf = [
            'start' => $rangeResult['start'],
            'finish' => $this->getIsoDateForDayOfYear($dayNumberFirstHalfFinish),
            'answer' => false,
        ];
        $newRangeLastHalf = [
            'start' => $this->getIsoDateForDayOfYear($dayNumberLastHalfStart),
            'finish' => $rangeResult['finish'],
            'answer' => false,
        ];

        $newRangesWithoutExceeded = array_filter($this->rangeQueries, function($range) use ($rangeResult) {
            return $range['start'] !== $rangeResult['start'] && $range['finish'] !== $rangeResult['finish'];
        });

        array_push($newRangesWithoutExceeded, $newRangeFirstHalf, $newRangeLastHalf);
        
        $this->rangeQueries = $newRangesWithoutExceeded;
    }

    protected function getIsoDateForDayOfYear(int $day, int $year = 0)
    {
        $theYear = $year === 0 ? $this->year : $year;
        $date = \DateTime::createFromFormat('Y z', "{$theYear} {$day}");
        return $date->format('Y-m-d');
    }
}
