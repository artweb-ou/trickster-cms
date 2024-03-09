<?php

namespace App\Logging;

/**
 * DTO for formatted display of log record information.
 */
final class FormattedLogRecordDTO
{
    public string $requestId;
    public string $ip;
    public string $url;
    public string $userAgent;
    public string $formattedStartTime;
    public string $formattedDuration;

    public function __construct(string $requestId, string $ip, string $url, string $userAgent, string $formattedStartTime, string $formattedDuration)
    {
        $this->requestId = $requestId;
        $this->ip = $ip;
        $this->url = $url;
        $this->userAgent = $userAgent;
        $this->formattedStartTime = $formattedStartTime;
        $this->formattedDuration = $formattedDuration;
    }
}
