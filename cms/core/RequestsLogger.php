<?php

use App\Logging\LogRecordDTO;
use App\Logging\LogRequestDTO;
use App\Logging\LogRecordUpdateDTO;
use App\Logging\RedisRequestLogger;

trait RequestsLogger
{
    protected ?LogRecordDTO $loggedRequestDto = null;

    private function logRequest(): void
    {
        try {
            $requestLogger = $this->getRequestLogger();
            $requestDto = new LogRequestDTO(
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['REQUEST_URI'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                microtime(true)
            );
            $this->loggedRequestDto = $requestLogger->logRequest($requestDto);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
        }
    }

    private function logRequestDuration(): void
    {
        if ($this->loggedRequestDto) {
            try {
                $requestLogger = $this->getRequestLogger();
                $updateDTO = new LogRecordUpdateDTO($this->loggedRequestDto->requestId, $this->loggedRequestDto->startTime, microtime(true));
                $requestLogger->updateDuration($updateDTO);
            } catch (Exception $e) {
                $this->logError($e->getMessage());
            }
        }
    }

    private RedisRequestLogger $requestLogger;

    private function getRequestLogger(): RedisRequestLogger
    {
        return $this->getService('RedisRequestLogger');
    }
}