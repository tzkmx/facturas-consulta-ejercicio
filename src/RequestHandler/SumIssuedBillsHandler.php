<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class SumIssuedBillsHandler implements HandlerInterface
{
    public function handle(RequestForYearInterface $request): RequestForYearInterface
    {
        if (isset($request['pendingQueries'])) {
            throw new \RuntimeException('Consultas Pendientes: Tiene consultas pendientes?');
        }
        if (!$request['isComplete']) {
            throw new \RuntimeException('Year Incomplete: ¿Año incompleto?');
        }
        if (isset($request['successQueries'])) {
            $request['billsIssued'] = $this->sumBills($request['successQueries']);
        }
        return $request;
    }

    protected function sumBills(array $successQueries): int
    {
        return array_reduce($successQueries, function ($initial, $success) {
            return $initial + $success['billsIssued'];
        }, 0);
    }
}
