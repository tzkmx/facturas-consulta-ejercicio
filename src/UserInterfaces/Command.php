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

        $rawReport = $this->resolveClientBillsService->getReport($clientId, $year);

        if ($rawReport['clientId'] === $clientId) {
            $billsIssued = $rawReport['billsIssued'];
            $queriesFetched = $rawReport['queriesFetched'];
            return "Para cliente con Id: {$clientId} " .
                "se emitieron {$billsIssued} facturas en {$year}. " .
                "El proceso requirió {$queriesFetched} consulta remota.";
        }
    }
}
