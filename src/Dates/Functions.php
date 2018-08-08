<?php

namespace Jefrancomix\ConsultaFacturas\Dates;

class Functions
{
    public static function getDateFromDayOfYear(int $day, int $year)
    {
        $date = \DateTime::createFromFormat('Y z', "{$year} {$day}");
        return $date->format('Y-m-d');
    }
    public static function getDayOfYearFromDate(string $date)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $date);
        return intval($date->format('z'));
    }
    public static function getNumberOfDaysInRange(string $start, string $finish)
    {
        list($startYear, , ) = explode('-', $start);
        list($finishYear, , )  = explode('-', $finish);
        if ($startYear !== $finishYear) {
            throw new \OutOfRangeException(__FUNCTION__ .
                ' solo debe ser usada para calcular rangos del mismo año');
        }
        
        $startDayNumber = Functions::getDayOfYearFromDate($start);
        $finishDayNumber = Functions::getDayOfYearFromDate($finish);
        if ($startDayNumber >= $finishDayNumber) {
            throw new \OutOfRangeException('La fecha de inicio debe ser mayor que la fecha final');
        }

        return ($finishDayNumber - $startDayNumber) + 1;
    }
}
