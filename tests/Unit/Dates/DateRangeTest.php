<?php

namespace Unit\Dates;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function testGetRangeOfDays()
    {
        $start = '2018-01-01';
        $finish = '2018-01-03';

        $range = new DateRange($start, $finish);

        $expectedArray = [ $start, $finish ];

        $this->assertEquals($expectedArray, $range->toArray());

        $this->assertEquals(['start'=>$start, 'finish'=>$finish], $range->toArray(true));
    }

    /**
     * @dataProvider getRangesToTestIntersection
     * @param string $otherStart
     * @param string $otherEnd
     * @param bool $expectsIntersect
     */
    public function testIntersectionOfRanges(string $otherStart, string $otherEnd, bool $expectsIntersect)
    {
        $start = '2018-01-01';
        $finish = '2018-01-03';

        $range = new DateRange($start, $finish);
        
        $rangeToCompare = new DateRange($otherStart, $otherEnd);
        
        $intersect = $range->intersects($rangeToCompare);
        
        $this->assertEquals($expectsIntersect, $intersect);
        
        $this->assertEquals($expectsIntersect, $rangeToCompare->intersects($range));
    }
    
    public function getRangesToTestIntersection()
    {
        return [
            [ '2018-01-04', '2018-01-05', false ],
            [ '2017-12-30', '2017-12-31', false ],
            [ '2018-01-03', '2018-01-04', true ],
            [ '2018-01-02', '2018-01-04', true ],
            [ '2018-01-02', '2018-01-03', true ],
            [ '2017-12-31', '2018-01-02', true ],
            [ '2017-12-31', '2018-01-01', true ],
        ];
    }

    /**
     * @dataProvider getRangesToTestDaysCounted
     * @param string $start
     * @param string $end
     * @param int $expected
     */
    public function testDatesInRange(string $start, string $end, int $expected)
    {
        $range = new DateRange($start, $end);
        $this->assertEquals($expected, $range->getDaysInRange());
    }

    public function getRangesToTestDaysCounted()
    {
        return [
            [ '2018-01-04', '2018-01-05', 2 ],
            [ '2017-12-30', '2017-12-31', 2 ],
            [ '2018-01-03', '2018-01-04', 2 ],
            [ '2018-01-02', '2018-01-04', 3 ],
            [ '2018-01-02', '2018-01-03', 2 ],
            [ '2017-12-31', '2018-01-02', 3 ],
            [ '2017-12-31', '2018-01-01', 2 ],
        ];
    }

    public function testFromArrayConstructor()
    {
        $init = [
            'start' => '2017-01-01',
            'finish' => '2017-12-31',
        ];

        $range = DateRange::fromArray($init);

        $this->assertEquals($init['start'], $range->getStartDate('Y-m-d'));
        $this->assertEquals($init['finish'], $range->getEndDate('Y-m-d'));
    }
}
