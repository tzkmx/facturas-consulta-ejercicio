<?php

namespace Jefrancomix\ConsultaFacturas\Service;

use Jefrancomix\ConsultaFacturas\Query\QueryFactory;
use Jefrancomix\ConsultaFacturas\Request\RequestForYear;
use Jefrancomix\ConsultaFacturas\RequestHandler\HandlerInterface;

class ResolveClientBillsForYear
{
    protected $pipelineHandler;
    protected $queryFactory;

    public function __construct(
        HandlerInterface $pipelineHandler,
        QueryFactory $queryFactory
    ) {
        $this->pipelineHandler = $pipelineHandler;
        $this->queryFactory = $queryFactory;
    }

    public function getReport($clientId, $year)
    {
        $request = new RequestForYear($clientId, $year, $this->queryFactory);

        $report = $this->pipelineHandler->handle($request);

        return $report;
    }
}
