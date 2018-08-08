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
}
