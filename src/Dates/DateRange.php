<?php

namespace Jefrancomix\ConsultaFacturas\Dates;

class DateRange
{
    protected $decoratedPeriod;
    protected $startDate;
    protected $finishDate;
    protected $daysInRange;

    public function __construct(string $start, string $end)
    {
        if ($start === $end) {
            throw new \LogicException('Las fechas de inicio y fin son iguales');
        }
        $this->startDate = date_create_from_format(DATE_ISO8601, $start.'T00:00:00Z');
        $this->endDate = date_create_from_format(DATE_ISO8601, $end.'T00:00:00Z');
        $daysInRange = $this->endDate->diff($this->startDate);

        $this->decoratedPeriod = new \DatePeriod($this->startDate, $daysInRange, $this->endDate);
        $this->daysInRange = $daysInRange->days + 1;
    }

    public function toArray()
    {
        return [
            $this->getStartDate('Y-m-d'),
            $this->getEndDate('Y-m-d'),
        ];
    }
    public function toHash()
    {
        return array_combine(['start', 'finish'], $this->toArray());
    }
    public static function fromArray(array $init)
    {
        return new self($init['start'], $init['finish']);
    }

    public function getDaysInRange()
    {
        return $this->daysInRange;
    }

    public function getStartDate($format = null)
    {
        $date = $this->decoratedPeriod->getStartDate();
        return is_null($format) ? $date : $date->format($format);
    }
    public function getEndDate($format = null)
    {
        $date = $this->decoratedPeriod->getEndDate();
        return is_null($format) ? $date : $date->format($format);
    }

    public function intersects(DateRange $otherDateRange): bool
    {
        $thisStart = intval($this->getStartDate('U'));
        $thisEnd = intval($this->getEndDate('U'));

        $otherStart = $otherDateRange->getStartDate()->getTimestamp();

        if ($thisStart <= $otherStart && $otherStart <= $thisEnd) {
            return true;
        }

        $otherEnd = $otherDateRange->getEndDate()->getTimestamp();

        if ($thisStart <= $otherEnd && $otherEnd <= $thisEnd) {
            return true;
        }

        return false;
    }
}
