<?php

trait DbLoggableApplication
{
    protected $logFilePath;
    protected $connection;
    protected $transportObject;

    protected function startDbLogging()
    {
        if ($controller = controller::getInstance()) {
            $pathsManager = $controller->getPathsManager();
            if ($controller->getDebugMode()) {
                $this->connection = $this->getService('db');
                $this->logFilePath = $pathsManager->getPath('logs') . 'db_' . time() . '.log';

                $this->connection->enableQueryLog();

                if (!class_exists('pdoTransport')) {
                    $pathsManager = $this->getService('PathsManager');
                    $path = $pathsManager->getIncludeFilePath('modules/transportObjects/pdoTransport.class.php');
                    include_once($path);
                }
                $this->transportObject = pdoTransport::getInstance($this->getService('ConfigManager')->getConfig('transport'));
                $this->transportObject->setDebug(true);
            }
        }
    }

    protected function saveDbLog()
    {
        if ($this->logFilePath) {
            $text = '';
            if ($this->transportObject) {
                if ($log = $this->transportObject->getQueriesHistory()) {
                    foreach ($log as $item) {
                        $text .= $item . ";\r\n";
                    }
                }
            }
            if ($this->connection) {
                if ($log = $this->connection->getQueryLog()) {
                    foreach ($log as $item) {
                        $query = $item['query'];
                        while (($position = stripos($query, '?')) !== false) {
                            $binding = "'" . array_shift($item['bindings']) . "'";
                            $query = substr_replace($query, $binding, stripos($query, '?'), 1);
                        }
                        $text .= $item['time'] . "\t" . $query . ";\r\n";
                    }
                }
            }
            file_put_contents($this->logFilePath, $text);
        }
    }
}