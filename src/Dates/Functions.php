<?php

namespace Jefrancomix\ConsultaFacturas\Dates;

use Jefrancomix\ConsultaFacturas\Exception\ExceptionBuilder;

class Functions
{
    use ExceptionBuilder;

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
        assert(self::getYearOf($start) === self::getYearOf($finish),
            self::getException('DateRange',
                'Esta función solo debe ser usada para calcular rangos del mismo año.',
                func_get_args()));

        $startDayNumber = self::getDayOfYearFromDate($start);
        $finishDayNumber = self::getDayOfYearFromDate($finish);

        assert($startDayNumber < $finishDayNumber,
            self::getException('DateRange',
                'La fecha de inicio debe ser menor que la fecha final.',
                func_get_args()));

        return ($finishDayNumber - $startDayNumber) + 1;
    }

    protected static function getYearOf(string $date): string
    {
        list($year, , ) = explode('-', $date);
        return $year;
    }
}
