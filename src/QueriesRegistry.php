<?php

namespace Jefrancomix\ConsultaFacturas;

class QueriesRegistry
{
    protected $rangeQueries;

    protected $clientId;

    protected $completed;

    protected $rangesBuilder;

    public function __construct(string $clientId, DatesRangesBuilder $rangesBuilder)
    {
        $this->clientId = $clientId;

        $this->completed = false;

        $this->rangesBuilder = $rangesBuilder;

        $initialQueryRange = $this->rangesBuilder->getNewRange();
        
        $this->rangeQueries = $this->getRegistryRangesWithAnswerFalse($initialQueryRange);
    }

    public function getStatus()
    {
        return [
            'completed' => $this->completed,
        ];
    }

    public function getRanges()
    {
        return $this->rangeQueries;
    }

    public function enterRangeQueryResult($result)
    {
        $answer = $result['answer'];
        if ($answer === 'excess') {
            $newRanges = $this->rangesBuilder->getNewRange($result);
            $strippedRange = $this->stripOldRange($result);
            $newRegistryEntries = $this->getRegistryRangesWithAnswerFalse($newRanges);
            $this->rangeQueries = $strippedRange + $newRegistryEntries;
            return;
        }
        $strippedRange = $this->stripOldRange($result);

        $this->rangeQueries = $strippedRange + [$result];

        $this->updateStatus();
    }

    protected function updateStatus()
    {
        $succededQueries = array_filter($this->rangeQueries, function($range) {
            return $range['answer'];
        });
        $allQueriesCount = count($this->rangeQueries);
        $succededQueriesCount = count($succededQueries);
        
        if ($allQueriesCount > 0 && ($allQueriesCount === $succededQueriesCount)) {
            $this->completed = true;
        }
    }

    protected function stripOldRange($rangeToStrip)
    {
        return array_filter($this->rangeQueries, function($range) use ($rangeToStrip) {
            return $range['start'] !== $rangeToStrip['start'] && $range['finish'] !== $rangeToStrip['finish'];
        });
    }

    protected function getRegistryRangesWithAnswerFalse($ranges)
    {
        return array_map(function($range) {
            $range['answer'] = false;
            return $range;
        }, $ranges);
    }
}
