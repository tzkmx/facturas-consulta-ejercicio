<?php

namespace Integration;

use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\UserInterfaces\Command;
use Jefrancomix\ConsultaFacturas\UserInterfaces\CommandInput;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function testBasicCommandRun()
    {
        $mockService = $this->createMock(ResolveClientBillsForYear::class);
        $mockService->method('getReport')
            ->willReturn([
                'clientId' => 'testing',
                'year' => '2017',
                'queriesFetched' => 1,
                'billsIssued' => 99,
                'successQueries' => [
                    [
                        'range' => [
                            'start' => '2017-01-01',
                            'finish' => '2017-12-31',
                        ],
                        'tries' => 1,
                        'billsIssued' => 99,
                    ],
                ],
            ]);

        $command = new Command($mockService);

        $inputArgsObj = new CommandInput();

        $inputArgs = [ // simulation of $argv
            'testing',
            '2017',
        ];

        $inputArgsObj->clientId = $inputArgs[0];
        $inputArgsObj->year = $inputArgs[1];

        $mockService->expects($this->once())
            ->method('getReport')
            ->with($inputArgs[0], $inputArgs[1]);


        $output = $command->run($inputArgsObj);

        $expectedOutput = 'Para cliente con Id: testing se emitieron 99 facturas en 2017. ' .
            'El proceso requirió 1 consulta remota.';

        $this->assertEquals($expectedOutput, $output);
    }
}
