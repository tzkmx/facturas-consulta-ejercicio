<?php

namespace Jefrancomix\ConsultaFacturas\Dates;

class DateRange
{
    protected $decoratedPeriod;
    protected $startDate;
    protected $finishDate;
    protected $daysInRange;

    private $timestampStart;
    private $timestampEnd;

    public function __construct(
        \DateTimeInterface $start,
        \DateTimeInterface $end
    ) {
        if ($start == $end) {
            throw new \LogicException('Las fechas de inicio y fin son iguales');
        }
        $this->startDate = $start;
        $this->finishDate = $end;
        $daysInRange = $this->finishDate->diff($this->startDate);

        $this->decoratedPeriod = new \DatePeriod(
            $this->startDate,
            $daysInRange,
            $this->finishDate
        );
        $this->daysInRange = $daysInRange->days + 1;

        $this->timestampStart = $start->getTimestamp();
        $this->timestampEnd = $end->getTimestamp();
    }

    public function toArray()
    {
        return [
            'start' => $this->getStartDate('Y-m-d'),
            'finish' => $this->getEndDate('Y-m-d'),
        ];
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
        $thisStart = $this->timestampStart;
        $thisEnd = $this->timestampEnd;

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
