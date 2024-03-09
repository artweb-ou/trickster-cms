<?php

namespace App\Logging;

use Redis;
use RedisException;

/**
 * Class for logging request information to Redis with automatic expiration and retrieval.
 */
class RedisRequestLogger
{
    private Redis $redis;
    private int $ttl;

    /**
     * RequestLogger constructor.
     *
     * @param Redis $redis Instance of Redis client.
     * @param int $ttl Time to live for log records in seconds.
     */
    public function __construct(Redis $redis, int $ttl)
    {
        $this->redis = $redis;
        $this->ttl = $ttl;
    }

    /**
     * Logs initial request information with automatic expiration.
     *
     * @param LogRequestDTO $dto DTO with request information.
     * @return LogRecordDTO DTO with log record information including requestId and startTime.
     * @throws RedisException
     */
    public function logRequest(LogRequestDTO $dto): LogRecordDTO
    {
        $requestId = uniqid('reqlog:', true);

        $data = [
            'ip' => $dto->ip,
            'url' => $dto->url,
            'start_time' => $dto->startTime,
            'user_agent' => $dto->userAgent,
            'duration' => 0,
        ];

        $this->redis->hMSet($requestId, $data);
        $this->redis->expire($requestId, $this->ttl);

        return new LogRecordDTO($requestId, $dto->ip, $dto->url, $dto->userAgent, $dto->startTime, 0);
    }

    /**
     * Updates the duration of the request execution.
     *
     * @param LogRecordUpdateDTO $updateDTO DTO with log record update information including requestId, startTime, and endTime.
     * @throws RedisException
     */
    public function updateDuration(LogRecordUpdateDTO $updateDTO): void
    {
        $duration = $updateDTO->endTime - $updateDTO->startTime;
        $this->redis->hSet($updateDTO->requestId, 'duration', $duration);
    }

    /**
     * Retrieves all log records from Redis, sorted by startTime in descending order, and returns them as a set of DTOs.
     *
     * @return array An array of LogRecordDTO objects.
     * @throws RedisException
     */
    public function getAllLogs(): array
    {
        $keys = $this->redis->keys('reqlog:*');
        $logs = [];

        foreach ($keys as $key) {
            $logData = $this->redis->hGetAll($key);
            if ($logData) {
                $logs[] = new LogRecordDTO(
                    $key,
                    $logData['ip'] ?? '',
                    $logData['url'] ?? '',
                    $logData['user_agent'] ?? '',
                    (float)$logData['start_time'] ?? 0,
                    (float)$logData['duration'] ?? 0
                );
            }
        }

        // Sort logs by startTime in descending order
        usort($logs, function ($log1, $log2) {
            return $log2->startTime - $log1->startTime;
        });

        return $logs;
    }
}
