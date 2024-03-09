<?php

namespace App\Logging;

/**
 * DTO for storing complete log record information.
 */
final readonly class LogRecordDTO
{
    public string $requestId;
    public string $ip;
    public string $url;
    public string $userAgent;
    public float $startTime;
    public float $duration;

    public function __construct(string $requestId, string $ip, string $url, string $userAgent, float $startTime, float $duration)
    {
        $this->requestId = $requestId;
        $this->ip = $ip;
        $this->url = $url;
        $this->userAgent = $userAgent;
        $this->startTime = $startTime;
        $this->duration = $duration;
    }
}