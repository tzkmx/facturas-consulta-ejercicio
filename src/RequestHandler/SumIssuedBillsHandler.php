<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Request\RequestForYearComplete;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class SumIssuedBillsHandler implements HandlerInterface
{
    public function handle(RequestForYearInterface $request): RequestForYearInterface
    {
        if (!$request->isComplete()) {
            throw new \RuntimeException('Request Incomplete');
        }
        return new RequestForYearComplete($request);
    }
}
