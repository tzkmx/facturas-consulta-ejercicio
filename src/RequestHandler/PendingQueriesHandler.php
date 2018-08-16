<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Jefrancomix\ConsultaFacturas\Query\QueryInterface;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class PendingQueriesHandler implements HandlerInterface
{
    private $request;

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle(RequestForYearInterface $request): RequestForYearInterface
    {
        $this->request = $request;

        $clientId = $request->clientId();

        foreach ($this->yieldPendingQueries() as $pendingQuery) {
            $this->doPendingQuery($pendingQuery, $clientId);
        }

        return $request;
    }

    protected function yieldPendingQueries()
    {
        $queriesIssued = 0;
        while (!$this->request->isComplete()) {
            yield ($this->request->getQueries())[$queriesIssued++];
        }
    }

    protected function doPendingQuery(QueryInterface $query, $clientId)
    {
        $fullQuery = $query->range()->toArray();
        $fullQuery['id'] = $clientId;
        $response = $this->client->get('/', [
            'query' => $fullQuery,
        ]);

        $this->handleResponse($query, $response);
    }

    protected function handleResponse(QueryInterface $query, Response $response)
    {
        $bodyResponse = $response->getBody()->getContents();

        $query->saveResult($bodyResponse);
    }
}
