<?php

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../src/QueriesRegistry.php';

use Jefrancomix\ConsultaFacturas\QueriesRegistry;

class QueriesRegistryTest extends TestCase
{
    public function testJustBuiltRegistryIsCompletedFalse()
    {
        $registry = new QueriesRegistry(2017, 'client');

        $justBuiltStatus = [
            'completed' => false,
          ];

        $this->assertEquals($justBuiltStatus, $registry->getStatus());
    }

    public function testJustBuiltRegistryCoversWholeYear()
    {
        $registry = new QueriesRegistry(2017, 'client');

        $rangesExpected = [
            [
                'start' => '2017-01-01',
                'finish' => '2017-12-31',
                'answer' => false,
            ],
        ];

        $this->assertEquals($rangesExpected, $registry->getRanges());
    }

    public function testExceededAmountOfBillsInRange()
    {
        $registry = new QueriesRegistry(2017, 'client');

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
}