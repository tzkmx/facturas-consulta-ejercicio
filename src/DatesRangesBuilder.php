<?php

namespace Jefrancomix\ConsultaFacturas;

use Jefrancomix\ConsultaFacturas\Dates\Functions;
use Jefrancomix\ConsultaFacturas\Exception\InvalidDateRangeException;

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
            throw new InvalidDateRangeException('A day range cannot be further split');
        }

        $start = \DateTime::createFromFormat(DATE_ISO8601, $oldRangeToSplit['start'].'T00:00:00Z');
        $end = \DateTime::createFromFormat(DATE_ISO8601, $oldRangeToSplit['finish'].'T00:00:00Z');

        $diff = $end->diff($start)->days + 1;

        $halfDiff = (($diff % 2) === 0) ? ($diff / 2) : (intdiv($diff, 2) + 1);

        $halfDiff--;

        $interval = \DateInterval::createFromDateString("{$halfDiff} days");

        $firstHalf = new \DatePeriod($start, $interval, 1);

        $period1Dates = iterator_to_array($firstHalf);

        $secondHalfStart = \DateTimeImmutable::createFromMutable($period1Dates[1])->add(new \DateInterval('P1D'));

        $newRangeFirstHalf = [
            'start' => $start->format('Y-m-d'),
            'finish' => $period1Dates[1]->format('Y-m-d'),
        ];
        $newRangeLastHalf = [
            'start' => $secondHalfStart->format('Y-m-d'),
            'finish' => $end->format('Y-m-d'),
        ];

        return [ $newRangeFirstHalf, $newRangeLastHalf ];
    }
}
