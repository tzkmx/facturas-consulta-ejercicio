<?php

namespace Unit\UserInterfaces;

use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\UserInterfaces\Command;
use Jefrancomix\ConsultaFacturas\UserInterfaces\CommandInput;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function testCallService()
    {
        $this->markTestSkipped('mock para devolver Request Completa?');
        $inputArgsObj = new CommandInput();

        $inputArgs = [ // simulation of $argv
            'testing',
            '2017',
        ];

        $inputArgsObj->clientId = $inputArgs[0];
        $inputArgsObj->year = $inputArgs[1];

        $mockService = $this->createMock(ResolveClientBillsForYear::class);

        $command = new Command($mockService);

        $mockService->expects($this->once())
            ->method('getReport')
            ->with($this->equalTo(
                'testing',
                2017
            ));

        $command->run($inputArgsObj);
    }
}
