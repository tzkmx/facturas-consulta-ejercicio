<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;

use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

interface HandlerInterface
{
    public function handle(RequestForYearInterface $request): RequestForYearInterface;
}
