<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class PendingQueriesHandler implements HandlerInterface
{
    protected $pendingQueries;
    protected $successQueries;
    protected $queriesFetched;

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handleRequest(array $request): array
    {
        $this->pendingQueries = $request['pendingQueries'];

        $clientId = $request['clientId'];

        foreach ($this->yieldPendingQueries() as $pendingQuery) {
            $this->doPendingQuery($pendingQuery, $clientId);
        }
        $request['pendingQueries'] = $this->pendingQueries;
        $request['successQueries'] = $this->successQueries;
        $request['queriesFetched'] = $this->queriesFetched;

        return $request;
    }

    protected function yieldPendingQueries()
    {
        $queriesIssued = 0;
        while (count($this->pendingQueries) > 0) {
            yield $this->pendingQueries[$queriesIssued++];
        }
        $this->queriesFetched = $queriesIssued;
    }

    protected function doPendingQuery($query, $clientId)
    {
        $fullQuery = $query;
        $fullQuery['id'] = $clientId;
        $response = $this->client->get('/', [
            'query' => $fullQuery,
        ]);

        $this->handleResponse($query, $response);
    }

    protected function handleResponse($resolvedQuery, Response $response)
    {
        $bodyResponse = $response->getBody()->getContents();

        if ($bodyResponse !== 'Hay más de 100 resultados') {
            $this->pendingQueries = array_filter($this->pendingQueries, function ($query) use ($resolvedQuery) {
                return $query['start'] !== $resolvedQuery['start'] && $query['finish'] !== $resolvedQuery['finish'];
            });
            $this->successQueries[] = [
                'range' => $resolvedQuery,
                'tries' => 1,
                'billsIssued' => (int)$bodyResponse,
            ];
        }
    }
}
