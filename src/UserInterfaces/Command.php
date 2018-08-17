<?php

namespace Jefrancomix\ConsultaFacturas\UserInterfaces;

use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;

class Command
{
    protected $resolveClientBillsService;

    public function __construct(ResolveClientBillsForYear $service)
    {
        $this->resolveClientBillsService = $service;
    }

    public function run(InputInterface $input)
    {
        $clientId = $input->getArgument('clientId');
        $year = $input->getArgument('year');
        $endpoint = $input->getArgument('endpoint');

        $completedReport = $this->resolveClientBillsService->getReport($clientId, $year, $endpoint);

        if ($completedReport->clientId() === $clientId) {
            $billsIssued = $completedReport->totalBills();
            $queriesFetched = $completedReport->totalQueries();
            $numberLegend = $queriesFetched > 1
                ? "consultas remotas"
                : "consulta remota";

            return "Para cliente con Id: {$clientId} " .
                "se emitieron {$billsIssued} facturas en {$year}. " .
                "El proceso requirió {$queriesFetched} {$numberLegend}.\n";
        }
    }
}
