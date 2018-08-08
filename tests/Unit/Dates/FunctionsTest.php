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

    /**
     * @dataProvider getBadRangesToTestDiffException
     * @expectedException \OutOfRangeException
     */
    public function testExceptionOfDaysInRange($startDate, $finishDate, $exceptionMessage)
    {
        $this->expectExceptionMessageRegExp($exceptionMessage);

        Functions::getNumberOfDaysInRange($startDate, $finishDate);
    }
    public function getBadRangesToTestDiffException()
    {
        return [
            [ '2017-01-01', '2018-01-01', '/solo.+para calcular rangos del mismo año/' ],
            [ '2017-01-31', '2017-01-01', '/inicio.+debe ser mayor que.+final/' ]
        ];
    }

    /**
     * @dataProvider getDatesRangesAndDiffs
     */
    public function testNumberOfDaysInRange($start, $finish, $expectedDiff)
    {
        $actualDiff = Functions::getNumberOfDaysInRange($start, $finish);
        $this->assertEquals($expectedDiff, $actualDiff);
    }
    public function getDatesRangesAndDiffs()
    {
        return [
            [ '2016-01-01', '2016-02-29', 60 ],
            [ '2018-01-01', '2018-02-28', 59 ],
        ];
    }
}
