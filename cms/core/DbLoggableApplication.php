<?php

trait DbLoggableApplication
{
    protected $logFilePath;
    protected $connection;

    protected function startDbLogging()
    {
        if ($controller = controller::getInstance()) {
            $pathsManager = $controller->getPathsManager();
            if ($controller->getDebugMode()) {
                $this->connection = $this->getService('db');
                $this->logFilePath = $pathsManager->getPath('logs') . 'db_' . time() . '.log';

                $this->connection->enableQueryLog();
            }
        }
    }

    protected function saveDbLog()
    {
        if ($this->connection) {
            if ($log = $this->connection->getQueryLog()) {
                $text = '';
                foreach ($log as $item) {
                    $query = $item['query'];
                    while (($position = stripos($query, '?')) !== false) {
                        $binding = "'" . array_shift($item['bindings']) . "'";
                        $query = substr_replace($query, $binding, stripos($query, '?'), 1);
                    }
                    $text .= $item['time'] . "\t" . $query . "\r\n";
                }
                file_put_contents($this->logFilePath, $text);
            }
        }
    }
}