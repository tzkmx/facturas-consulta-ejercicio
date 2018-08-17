<?php

namespace Integration;

use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\PipelineHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\SumIssuedBillsHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\UserInterfaces\Command;
use Jefrancomix\ConsultaFacturas\UserInterfaces\CommandInput;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
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

        $container['handler.service.pendingQueries'] = function ($c) {
            return new PendingQueriesHandler($c['handler.http.client']);
        };

        $container['handler.service.sumIssuedBills'] = function ($c) {
            return new SumIssuedBillsHandler();
        };
        $container['handler.service.pipeline'] = function ($c) {
            return new PipelineHandler(
                $c['handler.service.pendingQueries'],
                $c['handler.service.sumIssuedBills']
            );
        };
        $container['factory.datesRanges'] = function ($c) {
            return new DateRangeFactory();
        };
        $container['factory.query'] = function ($c) {
            return new QueryFactory($c['factory.datesRanges']);
        };
        $container['service.resolveIssuedBills'] = function ($c) {
            return new ResolveClientBillsForYear(
                $c['handler.service.pipeline'],
                $c['factory.query']
            );
        };

        $command = new Command($container['service.resolveIssuedBills']);

        $inputArgsObj = new CommandInput(
            'testing',
            '2017',
            'http://example.com/endpoint'
        );

        $output = $command->run($inputArgsObj);

        $expectedOutput = "Para cliente con Id: testing se emitieron 99 facturas en 2017. " .
            "El proceso requirió 1 consulta remota.\n";

        $this->assertEquals($expectedOutput, $output);
    }
}
