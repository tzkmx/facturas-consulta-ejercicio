<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;

class ValidateYearIsComplete implements HandlerInterface
{
    protected $cachedQueryRanges;

    public function handleRequest(array $request): array
    {
        $queriesToValidate = $request['successQueries'];
        $ranges = $this->getRangesFromQueries($queriesToValidate);

        $hasOverlapOfRanges = $this->hasOverlapOfRanges($ranges);

        if ($hasOverlapOfRanges) {
            $request['isComplete'] = false;
            return $request;
        }

        $year = $request['year'];

        $isRightNumberOfDays = $this->isRightNumberOfDaysCovered($year, $ranges);

        if (!$isRightNumberOfDays) {
            $request['isComplete'] = false;
            return $request;
        }

        unset($request['pendingQueries']);
        $request['isComplete'] = true;
        return $request;
    }

    protected function getRangesFromQueries(array $queries)
    {
        if (count($this->cachedQueryRanges)) {
            return $this->cachedQueryRanges;
        }

        $ranges = array_map(function ($query) {
            return $query['range'];
        }, $queries);

        $this->cachedQueryRanges = $ranges;

        return $ranges;
    }

    protected function isRightNumberOfDaysCovered($year, array $ranges): bool
    {
        $yearRange = [
            'start' => "{$year}-01-01",
            'finish' => "{$year}-12-31",
        ];
        $daysInYear = DateRange::fromArray($yearRange)->getDaysInRange();

        $daysInRanges = array_reduce($ranges, function ($init, $range) {
            return $init + DateRange::fromArray($range)->getDaysInRange();
        }, 0);

        return ($daysInRanges === $daysInYear);
    }

    protected function hasOverlapOfRanges(array $ranges): bool
    {
        $searchForOverlap = array_reduce($ranges, $this->searchEveryRangeForIntersection($ranges), 0);

        return $searchForOverlap === true;
    }

    protected function searchEveryRangeForIntersection(array $ranges)
    {
        return function ($indexOfItemOrFoundOverlap, $range) use ($ranges) {
            if ($indexOfItemOrFoundOverlap === true) {
                return true;
            }

            $dateRange = DateRange::fromArray($range);

            $foundOverlap = array_reduce(
                $ranges,
                $this->searchRangeIntersection($dateRange, $indexOfItemOrFoundOverlap),
                0
            );

            if ($foundOverlap === true) {
                return true;
            }
            return $indexOfItemOrFoundOverlap + 1;
        };
    }

    protected function searchRangeIntersection(DateRange $rangeToSearch, int $indexOfTheRange)
    {
        return function ($indexOfItemLookedOrTrue, $rangeToCompare) use ($rangeToSearch, $indexOfTheRange) {
            if ($indexOfItemLookedOrTrue === true) {
                return true;
            }

            if ($indexOfTheRange === $indexOfItemLookedOrTrue) {
                return $indexOfItemLookedOrTrue + 1;
            }

            $intersectedRanges = $rangeToSearch->intersects(DateRange::fromArray($rangeToCompare));

            return $intersectedRanges || ($indexOfItemLookedOrTrue + 1);
        };
    }
}
