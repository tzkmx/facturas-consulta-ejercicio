<?php

namespace Jefrancomix\ConsultaFacturas\UserInterfaces;

class CommandInput implements InputInterface
{
    public function getArgument(string $name)
    {
        $thisVars = get_object_vars($this);
        return isset($thisVars, $name) ? $thisVars[$name] : null;
    }
}
