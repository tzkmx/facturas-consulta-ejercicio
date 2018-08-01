<?php

namespace Jefrancomix\ConsultaFacturas;

class DatesRangesBuilder
{
    protected $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function getNewRange($oldRangeToSplit = null)
    {
        return is_null($oldRangeToSplit)
          ? $this->getDefaultRange()
          : $this->getSplitRange($oldRangeToSplit);
    }

    protected function getDefaultRange()
    {
        return [
            [
                'start' => "{$this->year}-01-01",
                'finish' => "{$this->year}-12-31",
            ]
        ];
    }

    protected function getSplitRange($oldRangeToSplit)
    {
        if ($oldRangeToSplit['start'] === $oldRangeToSplit['finish']) {
            throw new \RangeException('A day range cannot be further split');
        }
        $dayNumberStart = $this->getDayNumberFromIsoDate($oldRangeToSplit['start']);
        $dayNumberFinish = $this->getDayNumberFromIsoDate($oldRangeToSplit['finish']);

        $diff = ($dayNumberFinish - $dayNumberStart) + 1;

        $halfDiff = (($diff % 2) === 0) ? ($diff / 2) : (intdiv($diff, 2) + 1);

        $dayNumberFirstHalfFinish = ($dayNumberStart + $halfDiff) - 1;
        $dayNumberLastHalfStart = $dayNumberFirstHalfFinish + 1;

        $newRangeFirstHalf = [
            'start' => $oldRangeToSplit['start'],
            'finish' => $this->getIsoDateForDayOfYear($dayNumberFirstHalfFinish),
        ];
        $newRangeLastHalf = [
            'start' => $this->getIsoDateForDayOfYear($dayNumberLastHalfStart),
            'finish' => $oldRangeToSplit['finish'],
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