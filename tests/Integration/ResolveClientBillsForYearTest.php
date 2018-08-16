<?php

namespace Integration;

use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\RequestHandler\AddInitialRangeToRequest;
use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\SumIssuedBillsHandler;
use Jefrancomix\ConsultaFacturas\RequestHandler\ValidateYearIsComplete;
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

        $pendingQueriesHandler = new PendingQueriesHandler($client);
        $sumIssuedBillsHandler = new SumIssuedBillsHandler();

        $handler = new PipelineHandler(
            $pendingQueriesHandler,
            $sumIssuedBillsHandler
        );
        $dataRangeFactory = new DateRangeFactory();
        $queryFactory = new QueryFactory($dataRangeFactory);
        $service = new ResolveClientBillsForYear($handler, $queryFactory);
        $clientId = 'testing';
        $year = '2017';
        $report = $service->getReport($clientId, $year);

        $expectedReport = [
            'isComplete' => true,
            'totalQueries' => 1,
            'totalBills' => 99,
        ];
        $this->assertEquals($expectedReport['isComplete'], $report->isComplete());
        $this->assertEquals($expectedReport['totalQueries'], $report->totalQueries());
        $this->assertEquals($expectedReport['totalBills'], $report->totalBills());
    }
}
