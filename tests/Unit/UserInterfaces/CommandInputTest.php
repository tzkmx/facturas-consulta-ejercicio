<?php

namespace Unit\UserInterfaces;

use Jefrancomix\ConsultaFacturas\Service\ResolveClientBillsForYear;
use Jefrancomix\ConsultaFacturas\UserInterfaces\Command;
use Jefrancomix\ConsultaFacturas\UserInterfaces\CommandInput;
use PHPUnit\Framework\TestCase;

class CommandInputTest extends TestCase
{
    public function testCommandBadArguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        $inputArgsObj = new CommandInput('', '', '');
    }
    public function testCommandWithoutArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $inputArgsObj = new CommandInput();
    }
}
