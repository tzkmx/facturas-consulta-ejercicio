<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Exception\ExceptionBuilder;

class SumIssuedBillsHandler implements HandlerInterface
{
    use ExceptionBuilder;
    public function handleRequest(array $request): array
    {
        if (isset($request['pendingQueries'])) {
            throw self::getException('Pendientes', 'Tiene consultas pendientes?', []);
        }
        if (!$request['isComplete']) {
            throw self::getException('Incomplete', '¿Año incompleto?', []);
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
