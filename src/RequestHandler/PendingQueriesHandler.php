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

        $endpoint = $request->endpoint();

        while (!$this->request->isComplete()) {
            $this->doPendingQueries(
                $this->request->getQueries(),
                $endpoint
            );
            $this->request->updateStatus();
        }

        return $request;
    }

    protected function doPendingQueries(array $queries, $endpoint)
    {
        return array_reduce(
            $queries,
            function ($resolvedQueries, QueryInterface $query) use ($endpoint) {
                if (! $query->status() instanceof QueryStatusPending) {
                    return $resolvedQueries;
                }

                $resolved = $this->doPendingQuery($query, $endpoint);

                $resolvedQueries[] = $resolved;

                return $resolvedQueries;
            },
            []
        );
    }

    protected function doPendingQuery(QueryInterface $query, $endpoint)
    {
        $queryString = $query->toQueryString();

        $response = $this->client->get("{$endpoint}?{$queryString}");

        return $this->handleResponse($query, $response);
    }

    protected function handleResponse(QueryInterface $query, Response $response)
    {
        $bodyResponse = $response->getBody()->getContents();

        $query->saveResult($bodyResponse);

        return $query;
    }
}
