<?php

namespace App\Logging;

/**
 * DTO for updating log record information including requestId, startTime, and endTime.
 */
final readonly class LogRecordUpdateDTO
{
    public string $requestId;
    public float $startTime;
    public float $endTime;

    public function __construct(string $requestId, float $startTime, float $endTime)
    {
        $this->requestId = $requestId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
}