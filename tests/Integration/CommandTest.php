<?php

namespace Integration;

use Jefrancomix\ConsultaFacturas\RequestHandler\AddInitialRangeToRequest;
use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\PipelineHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\SumIssuedBillsHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Jefrancomix\ConsultaFacturas\RequestHandler\ValidateYearIsComplete;
use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\UserInterfaces\Command;
use Jefrancomix\ConsultaFacturas\UserInterfaces\CommandInput;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

class CommandTest extends TestCase
{
    public function testBasicCommandRun()
    {
        $container = new Container();

        $container['handler.http.mock'] = function ($c) {
            return new MockHandler([
                new Response('200', [], '99'),
            ]);
        };
        $container['handler.http.stack'] = function ($c) {
            return HandlerStack::create($c['handler.http.mock']);
        };
        $container['handler.http.client'] = function ($c) {
            return new Client(['handler' => $c['handler.http.mock']]);
        };

        $container['handler.service.initial'] = function ($c) {
            return new AddInitialRangeToRequest();
        };
        $container['handler.service.pendingQueries'] = function ($c) {
            return new PendingQueriesHandler($c['handler.http.client']);
        };
        $container['handler.service.validateYear'] = function ($c) {
            return new ValidateYearIsComplete();
        };
        $container['handler.service.sumIssuedBills'] = function ($c) {
            return new SumIssuedBillsHandler();
        };
        $container['handler.service.pipeline'] = function ($c) {
            return new PipelineHandler(
                $c['handler.service.initial'],
                $c['handler.service.pendingQueries'],
                $c['handler.service.validateYear'],
                $c['handler.service.sumIssuedBills']
            );
        };
        $container['service.resolveIssuedBills'] = function ($c) {
            return new ResolveClientBillsForYear($c['handler.service.pipeline']);
        };

        $command = new Command($container['service.resolveIssuedBills']);

        $inputArgsObj = new CommandInput();

        $inputArgs = [ // simulation of $argv
            'testing',
            '2017',
        ];

        $inputArgsObj->clientId = $inputArgs[0];
        $inputArgsObj->year = $inputArgs[1];

        $output = $command->run($inputArgsObj);

        $expectedOutput = 'Para cliente con Id: testing se emitieron 99 facturas en 2017. ' .
            'El proceso requirió 1 consulta remota.';

        $this->assertEquals($expectedOutput, $output);
    }
}
