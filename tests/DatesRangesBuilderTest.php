<?php

namespace Jefrancomix\ConsultaFacturas\Test;

use PHPUnit\Framework\TestCase;

use Jefrancomix\ConsultaFacturas\DatesRangesBuilder;

class DatesRangesBuilderTest extends TestCase
{
    public function testDefaultRangeWholeYear()
    {
        $builder = new DatesRangesBuilder(2017);

        $expectedRange = [
            [
              'start' => '2017-01-01',
              'finish' => '2017-12-31',
            ]
        ];

        $actualRange = $builder->getNewRange();

        $this->assertEquals($expectedRange, $actualRange);
    }

    public function testSplitOfWholeYear()
    {
        $builder = new DatesRangesBuilder(2017);

        $testRangeToSplit = [
            'start' => '2017-01-01',
            'finish' => '2017-12-31',
        ];

        $expectedRangesFromSplit = [
            [
                'start' => '2017-01-01',
                'finish' => '2017-07-02',
            ],
            [
                'start' => '2017-07-03',
                'finish' => '2017-12-31',
            ],
        ];

        $actualRanges = $builder->getNewRange($testRangeToSplit);

        $this->assertEquals($expectedRangesFromSplit, $actualRanges);
    }
}