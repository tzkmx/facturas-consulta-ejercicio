<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

class SumIssuedBillsHandler implements HandlerInterface
{
    public function handleRequest(array $request): array
    {
        if (count($request['pendingQueries']) === 0) {
            unset($request['pendingQueries']);
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
