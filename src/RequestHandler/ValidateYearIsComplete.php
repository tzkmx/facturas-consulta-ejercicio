<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Dates\Functions;

class ValidateYearIsComplete implements HandlerInterface
{
    protected $memoizedQueryRanges;

    public function handleRequest(array $request): array
    {
        $queriesToValidate = $request['successQueries'];
        $ranges = $this->getRangesFromQueries($queriesToValidate);

        $hasRepeatedDates = $this->hasRepeatedDates($ranges);

        if ($hasRepeatedDates) {
            $request['isComplete'] = false;
            return $request;
        }

        $hasOverlapedRanges = $this->hasOverlapedRanges($ranges);

        if ($hasOverlapedRanges) {
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
        if (count($this->memoizedQueryRanges)) {
            return $this->memoizedQueryRanges;
        }

        $ranges = array_map(function ($query) {
            return $query['range'];
        }, $queries);

        $this->memoizedQueryRanges = $ranges;

        return $ranges;
    }

    protected function isRightNumberOfDaysCovered($year, array $ranges): bool
    {
        $startOfYear = "{$year}-01-01";
        $endOfYear = "{$year}-12-31";
        $daysInYear = Functions::getNumberOfDaysInRange($startOfYear, $endOfYear);

        $daysCoveredByQueries = array_reduce($ranges, function ($init, $range) {
            return $init + Functions::getNumberOfDaysInRange($range['start'], $range['finish']);
        }, 0);

        return ($daysCoveredByQueries === $daysInYear);
    }

    protected function hasRepeatedDates(array $ranges): bool
    {
        $accumulatorInit = [
            'dates' => [],
            'repeated' => false
        ];

        $lookForRepeatedDates = array_reduce($ranges, function ($accumulator, $range) {
            // si ya encontramos fecha repetida solo seguimos indicándolo
            if ($accumulator['repeated']) {
                return $accumulator;
            }

            foreach (['start', 'finish'] as $keyDate) {
                $lookForDate = $range[$keyDate];

                // si fechas de inicio o final se encuentran en acumulador
                if (isset($accumulator['dates'][$lookForDate])) {
                    // ya no necesitaremos las fechas
                    unset($accumulator['dates']);
                    // pues ya sabemos que al menos una está repetida
                    $accumulator['repeated'] = true;
                    return $accumulator;
                }

                $accumulator['dates'][$lookForDate] = true;
            }
            return $accumulator;
        }, $accumulatorInit);

        return $lookForRepeatedDates['repeated'];
    }

    /**
     * Calculamos traslape de rangos, si fecha de inicio o fin de uno está entre otro rango.
     *
     * @param array $ranges
     * @return bool
     */
    protected function hasOverlapedRanges(array $ranges): bool
    {
        return array_reduce($ranges, function ($init, $range) use ($ranges) {
            // si ya encontramos traslape, solo cargamos el valor hasta el final
            if ($init) {
                return true;
            }
            $startToFind = Functions::getDayOfYearFromDate($range['start']);
            $finishToFind = Functions::getDayOfYearFromDate($range['finish']);

            $findOverlaped = array_reduce($ranges, function ($init, $range) use ($startToFind, $finishToFind) {
                // si ya encontramos traslape, solo cargamos el valor hasta el final
                if ($init) {
                    return true;
                }
                $startToCompare = Functions::getDayOfYearFromDate($range['start']);
                $finishToCompare = Functions::getDayOfYearFromDate($range['finish']);

                // es el mismo rango, no se traslapa pues
                if ($startToFind === $startToCompare && $finishToFind === $finishToCompare) {
                    return false;
                }

                if ($startToFind > $startToCompare && $startToFind < $finishToCompare) {
                    return true;
                }
                if ($finishToFind > $startToCompare && $finishToFind < $finishToCompare) {
                    return true;
                }
                return false;
            }, false);

            return $findOverlaped;
        }, false);
    }
}
