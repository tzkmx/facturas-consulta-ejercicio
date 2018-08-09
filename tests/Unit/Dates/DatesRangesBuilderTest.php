<?php

namespace Jefrancomix\ConsultaFacturas\Test;

use Jefrancomix\ConsultaFacturas\Exception\InvalidDateRangeException;
use PHPUnit\Framework\TestCase;

use Jefrancomix\ConsultaFacturas\DatesRangesBuilder;

class DatesRangesBuilderTest extends TestCase
{
    /**
     * @dataProvider rangesProvider
     */
    public function testRangesSplit($oldRangeToSplit, $expectedRanges)
    {
        $builder = new DatesRangesBuilder(2017);

        $actualRanges = $builder->getNewRange($oldRangeToSplit);

        $this->assertEquals($expectedRanges, $actualRanges);
    }

    public function rangesProvider()
    {
        return [
            'whole year split' => [
                [ 'start' => '2017-01-01', 'finish' => '2017-12-31' ],
                [
                    [ 'start' => '2017-01-01', 'finish' => '2017-07-02' ],
                    [ 'start' => '2017-07-03', 'finish' => '2017-12-31' ]
                ]
            ],
            'half year split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-07-02' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-04-02' ],
                  [ 'start' => '2017-04-03', 'finish' => '2017-07-02' ]
              ]
            ],
            'quarter year split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-04-02' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-02-15' ],
                  [ 'start' => '2017-02-16', 'finish' => '2017-04-02' ]
              ]
            ],
            'eighth of year split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-02-15' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-01-23' ],
                  [ 'start' => '2017-01-24', 'finish' => '2017-02-15' ]
              ]
            ],
            'sixteenth of year split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-01-24' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-01-12' ],
                  [ 'start' => '2017-01-13', 'finish' => '2017-01-24' ]
              ]
            ],
            '32th of year split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-01-13' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-01-07' ],
                  [ 'start' => '2017-01-08', 'finish' => '2017-01-13' ]
              ]
            ],
            '64th of year split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-01-07' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-01-04' ],
                  [ 'start' => '2017-01-05', 'finish' => '2017-01-07' ]
              ]
            ],
            '128th of year split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-01-04' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-01-02' ],
                  [ 'start' => '2017-01-03', 'finish' => '2017-01-04' ]
              ]
            ],
            'only two days split' => [
              [ 'start' => '2017-01-01', 'finish' => '2017-01-02' ],
              [
                  [ 'start' => '2017-01-01', 'finish' => '2017-01-01' ],
                  [ 'start' => '2017-01-02', 'finish' => '2017-01-02' ]
              ]
            ],
        ];
    }

    public function testExceptionOnStartSameAsFinishDate()
    {
        $builder = new DatesRangesBuilder(2017);
        $this->expectException(InvalidDateRangeException::class);

        $builder->getNewRange([
            'start' => '2017-01-01',
            'finish' => '2017-01-01'
        ]);
    }
}
