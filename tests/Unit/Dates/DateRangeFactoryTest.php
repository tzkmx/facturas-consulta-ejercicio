<?php

namespace Unit\Dates;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use PHPUnit\Framework\TestCase;

class DateRangeFactoryTest extends TestCase
{
    public function testBuildRangeWithOnlyYear()
    {
        $factory = new DateRangeFactory();

        $expectedRange = new DateRange('2017-01-01', '2017-12-31');

        $range = $factory->buildRangeForYear(2017);

        $this->assertEquals($expectedRange, $range);
    }
    public function testBuildRangeFromArray()
    {
        $factory = new DateRangeFactory();

        $expectedRange = new DateRange('2017-01-01', '2017-12-31');

        $arrayRange = [
            'start' => '2017-01-01',
            'finish' => '2017-12-31',
        ];

        $range = $factory->buildFromArray($arrayRange);

        $this->assertEquals($expectedRange, $range);
    }
}
