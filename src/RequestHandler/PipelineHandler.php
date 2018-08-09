<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

class PipelineHandler implements HandlerInterface
{
    protected $addInitialRange;

    protected $pendingQueriesHandler;

    protected $sumIssuedBillsHandler;

    protected $validateYearIsComplete;

    public function __construct(
        AddInitialRangeToRequest $addInitialRange,
        PendingQueriesHandler $pendingQueriesHandler,
        ValidateYearIsComplete $validateYearIsComplete,
        SumIssuedBillsHandler $sumIssuedBillsHandler
    ) {
        $this->addInitialRange = $addInitialRange;
        $this->pendingQueriesHandler = $pendingQueriesHandler;
        $this->validateYearIsComplete = $validateYearIsComplete;
        $this->sumIssuedBillsHandler = $sumIssuedBillsHandler;
    }

    public function handleRequest(array $request): array
    {
        $initialRequest = $this->addInitialRange->handleRequest($request);

        $resolvedQueriesRequest = $this->pendingQueriesHandler->handleRequest($initialRequest);

        $validatedCompleteYear = $this->validateYearIsComplete->handleRequest($resolvedQueriesRequest);

        $resolvedRequest = $this->sumIssuedBillsHandler->handleRequest($validatedCompleteYear);

        return $resolvedRequest;
    }
}
