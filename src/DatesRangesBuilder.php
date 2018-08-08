<?php

namespace Jefrancomix\ConsultaFacturas;

use Jefrancomix\ConsultaFacturas\Dates\Functions;

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
        $dayNumberStart = Functions::getDayOfYearFromDate($oldRangeToSplit['start']);
        $dayNumberFinish = Functions::getDayOfYearFromDate($oldRangeToSplit['finish']);

        $diff = ($dayNumberFinish - $dayNumberStart) + 1;

        $halfDiff = (($diff % 2) === 0) ? ($diff / 2) : (intdiv($diff, 2) + 1);

        $dayNumberFirstHalfFinish = ($dayNumberStart + $halfDiff) - 1;
        $dayNumberLastHalfStart = $dayNumberFirstHalfFinish + 1;

        $newRangeFirstHalf = [
            'start' => $oldRangeToSplit['start'],
            'finish' => Functions::getDateFromDayOfYear($dayNumberFirstHalfFinish, $this->year),
        ];
        $newRangeLastHalf = [
            'start' => Functions::getDateFromDayOfYear($dayNumberLastHalfStart, $this->year),
            'finish' => $oldRangeToSplit['finish'],
        ];

        return [ $newRangeFirstHalf, $newRangeLastHalf ];
    }
}
