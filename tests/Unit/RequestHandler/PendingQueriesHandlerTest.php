<?php

namespace Unit\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYear;
use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class PendingQueriesHandlerTest extends TestCase
{
    use MatchesSnapshots;
    private $request;
    private $solvedRequest;
    private $handler;
    private $container;

    public function testHandleInitialQueryWithSuccess()
    {
        $this->givenTheInitialRequest();
        $this->givenTheSolutionIsInTheFirstResponse();

        $this->whenTheHandlerPutItsHandsOnTheRequest();

        $this->thenTheResultShouldBe(
            $isComplete = true,
            $expectedResults = [99],
            $expectedTries = [1],
            $expectedDateRanges = [
                ['start' => '2017-01-01', 'finish' => '2017-12-31']
            ]
        );
        $this->assertMatchesSnapshot($this->dumpHistory());
    }

    private function givenTheInitialRequest()
    {
        $dateRangesFactory = new DateRangeFactory();
        $queryFactory = new QueryFactory($dateRangesFactory);
        $this->request = new RequestForYear(
            'testing',
            2017,
            $queryFactory,
            'http://example.com/endpoint'
        );
        $initialQueries = $this->request->getQueries();
        $this->assertEquals(
            ['start' => '2017-01-01', 'finish' => '2017-12-31'],
            $initialQueries[0]->range()->toArray()
        );
    }
    private function givenTheSolutionIsInTheFirstResponse()
    {
        $mockHttpHandler = new MockHandler([
            new Response('200', [], '99'),
        ]);

        $this->container = [];
        $history = Middleware::history($this->container);

        $stack = HandlerStack::create($mockHttpHandler);
        $stack->push($history);

        $client = new Client([
            'base_uri' => 'http://example.com/bills',
            'handler' => $stack
        ]);

        $this->handler = new PendingQueriesHandler($client);
    }
    private function whenTheHandlerPutItsHandsOnTheRequest()
    {
        $this->solvedRequest = $this->handler->handle($this->request);
    }
    private function thenTheResultShouldBe(
        bool $isComplete,
        array $expectedResults,
        array $expectedTries,
        array $expectedDateRanges
    ) {
        $this->assertEquals(
            $isComplete,
            $this->solvedRequest->isComplete(),
            'Mismatch completion result'
        );
        $queries = $this->solvedRequest->getQueries();

        $actualResults = array_map(
            function ($query) {
                return $query->result();
            },
            $queries
        );
        $actualTries = array_map(
            function ($query) {
                return $query->tries();
            },
            $queries
        );
        $actualRanges = array_map(
            function ($query) {
                return $query->range()->toArray();
            },
            $queries
        );

        $this->assertEquals($expectedResults, $actualResults, 'Mismatch queries results');
        $this->assertEquals($expectedTries, $actualTries, 'Mismatch count of tries');
        $this->assertEquals($expectedDateRanges, $actualRanges, 'DateRanges out of sync');
    }
    private function dumpHistory()
    {
        $results = [];
        foreach ($this->container as $transaction) {
            $req = $transaction['request'];
            $request = [
                'host' => $req->getUri()->getHost(),
                'path' => $req->getUri()->getPath(),
                'query' => $req->getUri()->getQuery(),
            ];
            $res = $transaction['response'];
            $response = $res->getBody().'';
            $error = '';
            if ($transaction['error']) {
                $error = var_export($transaction['error'], true);
            }
            $results[] = compact('request', 'response', 'error');
        }
        return $results;
    }
}
