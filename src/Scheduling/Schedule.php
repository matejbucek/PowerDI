<?php

namespace PowerDI\Scheduling;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Schedule {
    private int $frequency;

    public function __construct(int $frequency) {
        $this->frequency = $frequency;
    }

    public function getFrequency(): int {
        return $this->frequency;
    }
}