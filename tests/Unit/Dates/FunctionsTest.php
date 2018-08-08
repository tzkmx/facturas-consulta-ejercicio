<?php

namespace Unit\Dates;

use Jefrancomix\ConsultaFacturas\Dates\Functions;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider getDatesFromDayOfYear
     */
    public function testGetDateFromDayOfYear($day, $year, $expectedDate)
    {
        $convertedDate = Functions::getDateFromDayOfYear($day, $year);
        $this->assertEquals($expectedDate, $convertedDate);
    }
    public function getDatesFromDayOfYear()
    {
        return [
            '29 de Febrero de 2016' => [ 59, 2016, '2016-02-29' ],
            '1 de Marzo de 2018' => [ 59, 2018, '2018-03-01' ],
        ];
    }

    /**
     * @dataProvider getDaysOfYearFromDate
     */
    public function testGetDayOfYearFromDate($date, $expectedDay)
    {
        $convertedDay = Functions::getDayOfYearFromDate($date);
        $this->assertEquals($expectedDay, $convertedDay);
    }
    public function getDaysOfYearFromDate()
    {
        return [
            '29 de Febrero de 2016' => [ '2016-02-29', 59 ],
            '1 de Marzo de 2018' => [ '2018-03-01', 59 ],
        ];
    }
}
