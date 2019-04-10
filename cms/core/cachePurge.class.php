<?php

class cachePurge
{
    const MARKER_FILE_NAME = '_marker';
    protected $interval = 60; // how often to purge (seconds)
    protected $minAge = 300; // how old files need to be (seconds)
    protected $maxDeletions = 1000; // don't delete more files than that
    protected $path = '';
    protected $markerPath = '';
    protected $deletions = 0;

    public function __construct($path, $interval = 60, $minAge = 300, $maxDeletions = 1000)
    {
        $this->path = $path;
        $this->markerPath = $path . self::MARKER_FILE_NAME;
        $this->minAge = $minAge;
        $this->maxDeletions = $maxDeletions;
        $this->interval = $interval;
    }

    public function __destruct()
    {
        $date = $this->getLastPurgeDate();
        if ($date !== false && time() - $date >= $this->interval) {
            $this->purge();
        }
    }

    public function purge()
    {
        $now = time();
        if ($handler = opendir($this->path)) {
            while (($fileName = readdir($handler)) !== false) {
                $filePath = $this->path . $fileName;
                if (!file_exists($filePath) || $fileName === '.' || $fileName === '..' || $fileName === self::MARKER_FILE_NAME) {
                    continue;
                }
                if ($now - @filemtime($filePath) > $this->minAge) {
                    if (is_file($filePath)) {
                        @unlink($filePath);
                        ++$this->deletions;
                    } elseif (is_dir($filePath)) {
                        $this->purgeDirectory($filePath);
                    }
                    if ($this->deletions >= $this->maxDeletions) {
                        break;
                    }
                }
            }
            closedir($handler);
        }
        touch($this->markerPath);
    }

    protected function purgeDirectory($dir)
    {
        if ($directory_contents = @scandir($dir)) {
            foreach ($directory_contents as $item) {
                if ($item == '.' || $item == '..') {
                    continue;
                }
                $filePath = $dir . '/' . $item;
                if (is_file($filePath)) {
                    @unlink($filePath);
                    ++$this->deletions;
                }
            }
            return @rmdir($dir);
        }
        return false;
    }

    protected function getLastPurgeDate()
    {
        $date = false;
        if (!is_file($this->markerPath)) {
            file_put_contents($this->markerPath, ' ');
            $defaultCachePermissions = controller::getInstance()
                ->getConfigManager()
                ->get('paths.defaultCachePermissions');
            chmod($this->markerPath, $defaultCachePermissions);
        }
        if (is_file($this->markerPath)) {
            $date = filemtime($this->markerPath);
        }
        return $date;
    }
}