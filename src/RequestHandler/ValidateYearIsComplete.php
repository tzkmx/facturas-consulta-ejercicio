<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Dates\Functions;

class ValidateYearIsComplete implements HandlerInterface
{
    public function handleRequest(array $request): array
    {
        $year = $request['year'];
        $startOfYear = "${year}-01-01";
        $endOfYear = "${year}-12-31";
        $daysInYear = Functions::getNumberOfDaysInRange($startOfYear, $endOfYear);

        $queriesToValidate = $request['successQueries'];

        $daysCoveredBySuccessQueries = array_reduce($queriesToValidate, function ($init, $query) {
            $range = $query['range'];
            return $init + Functions::getNumberOfDaysInRange($range['start'], $range['finish']);
        }, 0);

        $wholeYearIsCovered = $daysCoveredBySuccessQueries === $daysInYear;

        if ($wholeYearIsCovered) {
            unset($request['pendingQueries']);
            $request['isComplete'] = true;
            return $request;
        } else {
            $request['isComplete'] = false;
            return $request;
        }
    }
}
