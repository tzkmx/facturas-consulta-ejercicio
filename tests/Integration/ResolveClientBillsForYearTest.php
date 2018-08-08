<?php

namespace Integration;

use Jefrancomix\ConsultaFacturas\RequestHandler\AddInitialRangeToRequest;
use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\SumIssuedBillsHandler;
use PHPUnit\Framework\TestCase;
use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\RequestHandler\PipelineHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ResolveClientBillsForYearTest extends TestCase
{
    public function testServiceReportsBillsAndQueriesFetched()
    {
        $mockHttpHandler = new MockHandler([
            new Response('200', [], '99'),
        ]);
        $stack = HandlerStack::create($mockHttpHandler);
        $client = new Client(['handler' => $stack]);

        $initialHandler = new AddInitialRangeToRequest();
        $pendingQueriesHandler = new PendingQueriesHandler($client);
        $sumIssuedBillsHandler = new SumIssuedBillsHandler();

        $handler = new PipelineHandler(
            $initialHandler,
            $pendingQueriesHandler,
            $sumIssuedBillsHandler
        );
        $service = new ResolveClientBillsForYear($handler);
        $clientId = 'testing';
        $year = '2017';
        $report = $service->getReport($clientId, $year);

        $expectedReport = [
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
        ];
        $this->assertEquals($expectedReport, $report);
    }
}
