<?php

namespace Jefrancomix\ConsultaFacturas\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;

class Query implements QueryInterface
{
    protected $range;
    protected $tries;
    protected $result;
    protected $status;
    protected $error;

    public function __construct(DateRange $range)
    {
        $this->range = $range;
        $this->tries = 0;
        $this->result = 0;
        $this->status = new QueryStatusPending();
        $this->error = '';
    }

    public function range(): DateRange
    {
        return $this->range;
    }

    public function tries(): int
    {
        return $this->tries;
    }

    public function result(): int
    {
        return $this->result;
    }

    public function status(): QueryStatus
    {
        return $this->status;
    }

    public function error(): string
    {
        return $this->error;
    }

    public function saveResult(string $result)
    {
        $this->tries++;
        $this->result = (int)$result;

        if ($result === 'Hay más de 100 resultados') {
            $this->status = new QueryStatusRangeExceededThreshold();
        } elseif (strpos($result, 'Error: ') === 0) {
            $this->status = new QueryStatusEndpointError();
            $this->error = $result;
        } else {
            $this->status = new QueryStatusResultOk();
        }
    }
}
