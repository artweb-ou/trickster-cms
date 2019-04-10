<?php

class perfomanceLog
{
    protected $startTime;
    protected $endTime;
    protected $log = [];
    protected $logFilePath;
    /** @var perfomanceLog */
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new perfomanceLog();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->setStartTime();
        date_default_timezone_set('UTC');
    }

    public function setStartTime($time = null)
    {
        if (is_null($time)) {
            $time = microtime(true);
        }
        $this->startTime = $time;
    }

    public function setEndTime($time = null)
    {
        if (is_null($time)) {
            $time = microtime(true);
        }
        $this->endTime = $time;
    }

    public function setOperationStartTime($operationName, $time = null)
    {
        if (is_null($time)) {
            $time = microtime(true);
        }
        $this->log[$operationName]['start'] = $time;
    }

    public function setOperationEndTime($operationName, $time = null)
    {
        if (is_null($time)) {
            $time = microtime(true);
        }
        $this->log[$operationName]['end'] = $time;
    }

    public function setOperationInfo($operationName, $info)
    {
        $this->log[$operationName]['info'] = $info;
    }

    public function setLogFilePath($path)
    {
        $this->logFilePath = $path;
    }

    public function writeLog()
    {
        if (!$this->endTime) {
            $this->setEndTime();
        }

        if (count($this->log)) {
            $contents = $this->getLogFileContents();
            $contents .= $this->generateLog();

            file_put_contents($this->logFilePath, $contents);
        }
    }

    protected function generateLog()
    {
        $contents = 'Date: ' . date('d.m.Y h:i:s', $this->startTime) . ' ';
        $contents .= 'Overall: ' . round($this->endTime - $this->startTime, 5);
        $contents .= "\n";

        foreach ($this->log as $operationName => &$item) {
            $contents .= '[' . round($item['end'] - $item['start'], 5) . "] \t";
            $contents .= '[' . round(100 * ($item['end'] - $item['start']) / ($this->endTime - $this->startTime), 2) . "%] \t";
            $contents .= '[' . $operationName . "] \t\t";
            if (isset($item['info'])) {
                $contents .= '[' . $item['info'] . '] ';
            }
            $contents .= "\n";
        }
        $contents .= "\n";
        return $contents;
    }

    protected function getLogFileContents()
    {
        if (file_exists($this->logFilePath)) {
            $logContents = file_get_contents($this->logFilePath);
            return $logContents;
        } else {
            $empty = '';
            return $empty;
        }
    }

    public function getLog()
    {
        return nl2br($this->generateLog());
    }
}