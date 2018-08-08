<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

class AddInitialRangeToRequest implements HandlerInterface
{
    public function handleRequest(array $request): array
    {
        $year = $request['year'];

        $firstQueryRange = [
            'start' => "${year}-01-01",
            'finish' => "${year}-12-31",
        ];

        $request['pendingQueries'] = [$firstQueryRange];

        return $request;
    }
}
