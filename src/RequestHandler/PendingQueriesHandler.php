<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Jefrancomix\ConsultaFacturas\Query\QueryInterface;
use Jefrancomix\ConsultaFacturas\Query\QueryStatusPending;
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
        $endpoint = $request->endpoint();

        foreach ($this->yieldPendingQueries() as $pendingQuery) {
            $this->doPendingQuery($pendingQuery, $clientId, $endpoint);
        }

        return $request;
    }

    protected function yieldPendingQueries()
    {
        while (!$this->request->isComplete()) {
            $queries = $this->request->getQueries();

            $pend = array_slice(array_filter(
                $queries,
                function ($query) {
                    return $query->status() instanceof QueryStatusPending;
                }
            ), 0);

            $query = $pend[0];

            yield $query;
        }
    }

    protected function doPendingQuery(QueryInterface $query, $clientId, $endpoint)
    {
        $fullQuery = $query->range()->toArray();
        $fullQuery['id'] = $clientId;
        $response = $this->client->get($endpoint, [
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
