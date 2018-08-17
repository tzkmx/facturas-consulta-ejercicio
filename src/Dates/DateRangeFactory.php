<?php

namespace Jefrancomix\ConsultaFacturas\Dates;

use Jefrancomix\ConsultaFacturas\Exception\InvalidDateRangeException;

class DateRangeFactory
{
    public function buildRangeForYear(int $year): DateRange
    {
        $start = "{$year}-01-01";
        $finish = "{$year}-12-31";

        $range = $this->buildFromStrings($start, $finish);
        return $range;
    }
    public function buildFromStrings(string $start, string $finish): DateRange
    {
        $startDate = date_create_from_format(DATE_ISO8601, $start.'T00:00:00Z');
        $endDate = date_create_from_format(DATE_ISO8601, $finish.'T00:00:00Z');
        if ($startDate === false || $endDate === false) {
            throw new \InvalidArgumentException('Invalid Format of Date String passed');
        }
        return new DateRange($startDate, $endDate);
    }

    public function buildFromArray(array $init): DateRange
    {
        return $this->buildFromStrings($init['start'], $init['finish']);
    }

    public function buildArrayOfRangesSplitting(DateRange $range): array
    {
        $start = $range->getStartDate();
        $end = $range->getEndDate();

        if ($start == $end) {
            throw new InvalidDateRangeException(
                'One day range is the minimal range, cannot be further split'
            );
        }

        $diff = $end->diff($start)->days + 1;

        $halfDiff = (($diff % 2) === 0) ? ($diff / 2) : (intdiv($diff, 2) + 1);

        $halfDiff--;

        $interval = \DateInterval::createFromDateString("{$halfDiff} days");

        $firstHalf = new \DatePeriod($start, $interval, 1);

        list(, $endFirstHalf) = iterator_to_array($firstHalf);

        $period2Start = (
            get_class($endFirstHalf) === 'DateTimeImmutable'
        )
            ? $endFirstHalf->add(new \DateInterval('P1D'))
            : \DateTimeImmutable::createFromMutable($endFirstHalf)
                ->add(new \DateInterval('P1D'));

        $firstHalfRange = new DateRange($start, $endFirstHalf);
        $secondHalfRange = new DateRange($period2Start, $end);
        return [ $firstHalfRange, $secondHalfRange ];
    }
}
