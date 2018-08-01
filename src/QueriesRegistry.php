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

        $this->rangeQueries = [
            [
              'start' => "{$year}-01-01",
              'finish' => "{$year}-12-31",
              'answer' => false,
            ]
        ];
    }

    protected function exceededAnswerInRangeThenBuildNewQueryRanges($rangeResult)
    {
        $newRangesByExceededSplittedInHalf = $this->getTwoRangesSplittingOneByHalf($rangeResult);

        $oldRangesWithoutExceeded = array_filter($this->rangeQueries, function($range) use ($rangeResult) {
            return $range['start'] !== $rangeResult['start'] && $range['finish'] !== $rangeResult['finish'];
        });
        
        $this->rangeQueries = $oldRangesWithoutExceeded + $newRangesByExceededSplittedInHalf;
    }

    protected function getTwoRangesSplittingOneByHalf($bigRange)
    {
        $dayNumberStart = $this->getDayNumberFromIsoDate($bigRange['start']);
        $dayNumberFinish = $this->getDayNumberFromIsoDate($bigRange['finish']);

        $diff = $dayNumberFinish - $dayNumberStart;

        $halfDiff = (($diff % 2) === 0) ? ($diff / 2) : (intdiv($diff, 2) + 1);

        $dayNumberFirstHalfFinish = $dayNumberStart + $halfDiff;
        $dayNumberLastHalfStart = $dayNumberFirstHalfFinish + 1;

        $newRangeFirstHalf = [
            'start' => $bigRange['start'],
            'finish' => $this->getIsoDateForDayOfYear($dayNumberFirstHalfFinish),
            'answer' => false,
        ];
        $newRangeLastHalf = [
            'start' => $this->getIsoDateForDayOfYear($dayNumberLastHalfStart),
            'finish' => $bigRange['finish'],
            'answer' => false,
        ];

        return [ $newRangeFirstHalf, $newRangeLastHalf ];
    }

    protected function getIsoDateForDayOfYear(int $day, int $year = 0)
    {
        $theYear = $year === 0 ? $this->year : $year;
        $date = \DateTime::createFromFormat('Y z', "{$theYear} {$day}");
        return $date->format('Y-m-d');
    }

    protected function getDayNumberFromIsoDate($isoDate)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $isoDate);
        return intval($date->format('z'));
    }
}
