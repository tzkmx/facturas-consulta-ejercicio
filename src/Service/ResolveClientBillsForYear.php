<?php

namespace Jefrancomix\ConsultaFacturas\Service;
use Jefrancomix\ConsultaFacturas\RequestHandler\HandlerInterface;

class ResolveClientBillsForYear
{
    protected $pipelineHandler;

    public function __construct(HandlerInterface $pipelineHandler)
    {
        $this->pipelineHandler = $pipelineHandler;
    }

    public function reportBillsOfClientForYear($clientId, $year)
    {
        $request = [
            'clientId' => $clientId,
            'year' => $year,
        ];
        $report = $this->pipelineHandler->handleRequest($request);

        return $report;
    }
}