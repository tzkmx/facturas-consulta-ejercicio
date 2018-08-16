<?php

namespace Unit\Dates;

use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Exception\InvalidDateRangeException;
use PHPUnit\Framework\TestCase;

class DateRangeFactoryTest extends TestCase
{
    /**
     * @var DateRangeFactory
     */
    private $factory;
    public function setUp()
    {
        $this->factory = new DateRangeFactory();
    }
    public function testBuildRangeWithOnlyYear()
    {
        $expectedRange = $this->factory->buildFromStrings(
            '2017-01-01',
            '2017-12-31'
        );

        $range = $this->factory->buildRangeForYear(2017);

        $this->assertEquals($expectedRange, $range);
    }
    public function testBuildFromStrings()
    {
        $expectedStart = date_create_from_format(
            DATE_ISO8601,
            '2018-01-01T00:00:00Z'
        );
        $expectedEnd = date_create_from_format(
            DATE_ISO8601,
            '2018-12-31T00:00:00Z'
        );
        $dateRange = $this->factory->buildFromStrings(
            $expectedStart->format('Y-m-d'),
            $expectedEnd->format('Y-m-d')
        );
        $this->assertEquals($expectedStart, $dateRange->getStartDate());
        $this->assertEquals($expectedEnd, $dateRange->getEndDate());
    }

    /**
     * @dataProvider getInvalidDateStrings
     * @param $wrongStart string to test illegal format
     */
    public function testBuildFromStringsException(string $wrongStart)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid Format of Date String passed'
        );
        $this->factory->buildFromStrings($wrongStart, $wrongStart);
    }
    public function getInvalidDateStrings()
    {
        return [
            [ '12-01-1990' ],
            [ 'May 4th, 1990' ],
            [ '1990-01-02 20:00' ],
            [ '1990-01-02T20:00' ],
            [ '1990-01-02T20:00:00Z' ],
            [ '1990-01-02T20:00:00-06:00' ],
        ];
    }
    public function testBuildRangeFromArray()
    {
        $expectedRange = $this->factory
            ->buildFromStrings('2017-01-01', '2017-12-31');

        $arrayRange = [
            'start' => '2017-01-01',
            'finish' => '2017-12-31',
        ];

        $range = $this->factory->buildFromArray($arrayRange);

        $this->assertEquals($expectedRange, $range);
    }
    public function testBuildArrayOfRangesSplittingProvidedOne()
    {
        $provided = $this->factory->buildFromStrings(
            '2017-01-01',
            '2017-12-31'
        );
        $expected = [
            $this->factory->buildFromStrings('2017-01-01', '2017-07-02'),
            $this->factory->buildFromStrings('2017-07-03', '2017-12-31'),
        ];
        $builtRanges = $this->factory->buildArrayOfRangesSplitting($provided);
        $this->assertEquals($expected, $builtRanges);
    }
}
