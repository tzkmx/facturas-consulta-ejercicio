<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Request\RequestForYearCompleteInterface;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class PipelineHandler implements HandlerInterface
{
    protected $pendingQueriesHandler;

    protected $sumIssuedBillsHandler;

    public function __construct(
        PendingQueriesHandler $pendingQueriesHandler,
        SumIssuedBillsHandler $sumIssuedBillsHandler
    ) {
        $this->pendingQueriesHandler = $pendingQueriesHandler;
        $this->sumIssuedBillsHandler = $sumIssuedBillsHandler;
    }

    public function handle(RequestForYearInterface $request): RequestForYearInterface
    {
        $processedRequest = $request;

        while (!$processedRequest instanceof RequestForYearCompleteInterface) {
            try {
                $processedRequest = $this->handlePendingRequest($processedRequest);
                $processedRequest = $this->completeRequest($processedRequest);
            } catch (\RuntimeException $e) {
                error_log($e->getMessage(), 0);
            }
        }

        return $processedRequest;
    }

    private function handlePendingRequest(RequestForYearInterface $requestForYear): RequestForYearInterface
    {
        return $this->pendingQueriesHandler->handle($requestForYear);
    }
    private function completeRequest(RequestForYearInterface $request): RequestForYearCompleteInterface
    {
        return $this->sumIssuedBillsHandler->handle($request);
    }
}
