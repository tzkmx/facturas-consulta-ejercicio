<?php

namespace Unit\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use PHPUnit\Framework\TestCase;


class PendingQueriesHandlerTest extends TestCase
{
    public function testHandleInitialQueryWithSuccess()
    {
        $mockHttpHandler = new MockHandler([
            new Response('200', [], '99'),
        ]);
        $stack = HandlerStack::create($mockHttpHandler);
        $client = new Client(['handler' => $stack]);

        $handler = new PendingQueriesHandler($client);

        $initialRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'pendingQueries' => [
                [
                    'start' => '2017-01-01',
                    'finish' => '2017-12-31',
                ],
            ],
        ];

        $resolvedRequest = $handler->handleRequest($initialRequest);

        $expectedResolvedRequest = [
            'clientId' => 'testing',
            'year' => '2017',
            'pendingQueries' => [],
            'queriesFetched' => 1,
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
        $this->assertEquals($expectedResolvedRequest, $resolvedRequest);
    }
}
