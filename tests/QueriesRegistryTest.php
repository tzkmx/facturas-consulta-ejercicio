<?php

namespace Jefrancomix\ConsultaFacturas\Test;

use PHPUnit\Framework\TestCase;

use Jefrancomix\ConsultaFacturas\QueriesRegistry;
use Jefrancomix\ConsultaFacturas\DatesRangesBuilder;

class QueriesRegistryTest extends TestCase
{
    public function testJustBuiltRegistryIsCompletedFalse()
    {
        $rangesBuilder = new DatesRangesBuilder(2017);
        $registry = new QueriesRegistry('client', $rangesBuilder);

        $justBuiltStatus = [
            'completed' => false,
          ];

        $this->assertEquals($justBuiltStatus, $registry->getStatus());
    }

    public function testExceededAmountOfBillsInRange()
    {
        $rangesBuilder = new DatesRangesBuilder(2017);
        $registry = new QueriesRegistry('client', $rangesBuilder);

        $rangeQueryResult = [
            'start' => '2017-01-01',
            'finish' => '2017-12-31',
            'answer' => 'excess',
        ];

        $registry->enterRangeQueryResult($rangeQueryResult);

        $statusExpected = ['completed' => false];
        $rangesExpected = [
            [
                'start' => '2017-01-01',
                'finish' => '2017-07-02',
                'answer' => false,
            ],
            [
                'start' => '2017-07-03',
                'finish' => '2017-12-31',
                'answer' => false,
            ],
        ];

        $this->assertEquals($statusExpected, $registry->getStatus());

        $this->assertEquals($rangesExpected, $registry->getRanges());
    }

    public function testCompletedRegistryOnFirstQuerySuccess()
    {
        $rangesBuilder = new DatesRangesBuilder(2017);
        $registry = new QueriesRegistry('client', $rangesBuilder);

        $rangeQueryResult = [
            'start' => '2017-01-01',
            'finish' => '2017-12-31',
            'answer' => '99',
        ];

        $registry->enterRangeQueryResult($rangeQueryResult);

        $statusExpected = ['completed' => true];
        $rangesExpected = [
            [
                'start' => '2017-01-01',
                'finish' => '2017-12-31',
                'answer' => '99',
            ],
        ];
        $this->assertEquals($statusExpected, $registry->getStatus());

        $this->assertEquals($rangesExpected, $registry->getRanges());
    }
}
