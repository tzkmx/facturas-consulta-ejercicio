<?php

namespace Unit\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Jefrancomix\ConsultaFacturas\Dates\DateRangeFactory;
use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYear;
use Jefrancomix\ConsultaFacturas\RequestHandler\PendingQueriesHandler;
use PHPUnit\Framework\TestCase;

/**
 * @group RequestHandlerRefactor
 */
class PendingQueriesHandlerTest extends TestCase
{
    private $request;
    private $solvedRequest;
    private $handler;

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
    }

    private function givenTheInitialRequest()
    {
        $dateRangesFactory = new DateRangeFactory();
        $queryFactory = new QueryFactory($dateRangesFactory);
        $this->request = new RequestForYear('testing', 2017, $queryFactory);
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
        $stack = HandlerStack::create($mockHttpHandler);
        $client = new Client(['handler' => $stack]);

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
}
