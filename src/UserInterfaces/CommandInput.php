<?php

namespace Jefrancomix\ConsultaFacturas\UserInterfaces;

class CommandInput implements InputInterface
{
    private $clientId;
    private $year;
    private $endpoint;

    public function __construct($clientId, $year, $endpoint)
    {
        $this->validateArgument('"Id de Cliente"', $clientId, '/[0-9a-f\-]+/');
        $this->clientId = $clientId;

        $this->validateArgument('Año', $year, '/[0-9]{4}/');
        $this->year = $year;

        $this->validateArgument(
            '"API consultas"',
            $endpoint,
            '/https?:\/\/[0-9a-zA-Z\.]+\/[0-9a-z]+/',
            'La dirección debe comenzar con http o https ;-)'
        );
        $this->endpoint = $endpoint;
    }

    private function validateArgument($name, $argString, $regexp, $messageIfFailed = '')
    {
        if (!preg_match($regexp, $argString)) {
            throw new \InvalidArgumentException(
                "Formato de argumento {$name} inválido. {$messageIfFailed}"
            );
        }
    }

    public function getArgument(string $name)
    {
        $thisVars = get_object_vars($this);
        return isset($thisVars, $name) ? $thisVars[$name] : null;
    }
}
