<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class ErrorLog
{
    private static ?self $instance = null;
    private readonly Logger $logger;

    private readonly string $defaultEnvironmentUrl;

    private function __construct()
    {
        $this->defaultEnvironmentUrl = 'http://localhost';

        $todayDate = date('Y-m-d');
        $pathsManager = controller::getInstance()->getPathsManager();
        $logFilePath = $pathsManager->getPath('logs') . $todayDate . '.txt';
        $this->logger = new Logger('error_log');
        $this->logger->pushHandler(new StreamHandler($logFilePath, Logger::DEBUG));
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @throws JsonException
     */
    public function logMessage(string $locationName, string $errorText, ?int $level = null): void
    {
        $level = match ($level) {
            E_ERROR => Logger::ERROR,
            E_WARNING => Logger::WARNING,
            E_NOTICE => Logger::NOTICE,
            default => Logger::DEBUG,
        };

        $logEntry = [
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
            'location' => $locationName,
            'message' => $errorText,
            'level' => $level,
            'url' => $this->getUrl(),
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];

        $this->logger->log($level, json_encode($logEntry, JSON_THROW_ON_ERROR));
    }

    private function getUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $this->defaultEnvironmentUrl;
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        return sprintf('%s://%s%s', $scheme, $host, $uri);
    }
}