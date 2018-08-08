<?php

namespace Jefrancomix\ConsultaFacturas\RequestHandler;


interface HandlerInterface
{
    public function handleRequest(array $request): array;
}