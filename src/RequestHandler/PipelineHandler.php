<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

class PipelineHandler implements HandlerInterface
{
    protected $addInitialRange;

    protected $pendingQueriesHandler;

    protected $sumIssuedBillsHandler;

    public function __construct(
        AddInitialRangeToRequest $addInitialRange,
        PendingQueriesHandler $pendingQueriesHandler,
        SumIssuedBillsHandler $sumIssuedBillsHandler
    ) {
        $this->addInitialRange = $addInitialRange;
        $this->pendingQueriesHandler = $pendingQueriesHandler;
        $this->sumIssuedBillsHandler = $sumIssuedBillsHandler;
    }

    public function handleRequest(array $request): array
    {
        $initialRequest = $this->addInitialRange->handleRequest($request);

        $resolvedQueriesRequest = $this->pendingQueriesHandler->handleRequest($initialRequest);

        $resolvedRequest = $this->sumIssuedBillsHandler->handleRequest($resolvedQueriesRequest);

        return $resolvedRequest;
    }
}
