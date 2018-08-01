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
            $this->substituteExceededRangeWithNewRanges($result);
            return;
        }
        $this->registerAnswerForRange($result);
    }

    protected function registerAnswerForRange($result)
    {
        array_walk($this->rangeQueries, function(&$range, $_, $result) {
            if ($range['start'] === $result['start'] && $range['finish'] === $result['finish']) {
              $range['answer'] = $result['answer'];
            }
        }, $result);

        $this->updateStatus();
    }

    protected function substituteExceededRangeWithNewRanges($oldRange)
    {
        $filteredRange = $this->withoutOldRange($oldRange);
        
        $newRanges = $this->rangesBuilder->getNewRange($oldRange);
        
        $newRegistryEntries = $this->getRegistryRangesWithAnswerFalse($newRanges);
        
        $this->rangeQueries = $filteredRange + $newRegistryEntries;
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

    protected function withoutOldRange($rangeToStrip)
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
