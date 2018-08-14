<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

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

    public function handle(RequestForYearInterface $request): RequestForYearInterface
    {
        $initialRequest = $this->addInitialRange->handle($request);

        $resolvedQueriesRequest = $this->pendingQueriesHandler->handle($initialRequest);

        $validatedCompleteYear = $this->validateYearIsComplete->handle($resolvedQueriesRequest);

        $resolvedRequest = $this->sumIssuedBillsHandler->handle($validatedCompleteYear);

        return $resolvedRequest;
    }
}
