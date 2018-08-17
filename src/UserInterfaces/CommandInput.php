<?php

namespace Jefrancomix\ConsultaFacturas\UserInterfaces;

class CommandInput implements InputInterface
{
    private $clientId;
    private $year;
    private $endpoint;

    public function getArgument(string $name)
    {
        $thisVars = get_object_vars($this);
        return isset($thisVars, $name) ? $thisVars[$name] : null;
    }
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
}
