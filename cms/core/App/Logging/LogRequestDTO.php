<?php

namespace App\Logging;

final readonly class LogRequestDTO
{
    public string $ip;
    public string $url;
    public string $userAgent;
    public float $startTime;

    public function __construct(string $ip, string $url, string $userAgent, float $startTime)
    {
        $this->ip = $ip;
        $this->url = $url;
        $this->userAgent = $userAgent;
        $this->startTime = $startTime;
    }
}