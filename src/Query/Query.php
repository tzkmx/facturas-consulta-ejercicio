<?php

namespace Jefrancomix\ConsultaFacturas\Query;

use Jefrancomix\ConsultaFacturas\Dates\DateRange;
use Jefrancomix\ConsultaFacturas\Request\RequestForYearInterface;

class Query implements QueryInterface
{
    protected $range;
    protected $tries;
    protected $result;
    protected $status;
    protected $error;
    private $queryString;

    public function __construct(DateRange $range, string $clientId)
    {
        $this->range = $range;
        $this->tries = 0;
        $this->result = 0;
        $this->status = new QueryStatusPending();
        $this->error = '';

        $args = $range->toArray();
        $args['id'] = $clientId;
        $this->queryString = http_build_query($args);
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

    /**
     * Unique method to mutate Query Status and variables.
     *
     * @SuppressWarnings(PHPMD.ElseExpression) multiple return would be preposterous here
     *
     * @param string $result receives the result from the QueryHandler
     */
    public function saveResult(string $result)
    {
        $this->tries++;
        $this->result = (int)$result;

        if ($result === '"Hay más de 100 resultados"') {
            $this->status = new QueryStatusRangeExceededThreshold();
        } elseif (strpos($result, 'Error: ') === 0) {
            $this->status = new QueryStatusEndpointError();
            $this->error = $result;
        } else {
            $this->status = new QueryStatusResultOk();
        }
    }

    public function toQueryString(): string
    {
        return $this->queryString;
    }
}
