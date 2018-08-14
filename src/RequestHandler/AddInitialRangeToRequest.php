<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class AddInitialRangeToRequest implements HandlerInterface
{
    public function handle(RequestForYearInterface $request): RequestForYearInterface
    {
        $year = $request['year'];

        $firstQueryRange = [
            'start' => "{$year}-01-01",
            'finish' => "{$year}-12-31",
        ];

        $request['pendingQueries'] = [$firstQueryRange];

        return $request;
    }
}
