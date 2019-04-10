<?php

class ApplicationCacheManager extends errorLogger
{
    const CONFIG_FILE = 'appcache.txt';
    const DISABLING_DELAY = 15; // how many minutes until enabled cache can be disabled
    protected static $instance;
    protected $enabled = false;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct()
    {
        $this->config = new ApplicationCacheConfig(self::CONFIG_FILE);
        $this->checkStatus();
    }

    protected function checkStatus()
    {
        if (!$this->config->enabled || (time() - $this->config->enablingTime > self::DISABLING_DELAY * 60)) {
            $this->enabled = LOAD_LEVEL_SEVERE <= loadManager::getLoadLevel();

            if ($this->enabled != $this->config->enabled) {
                $this->config->enabled = $this->enabled;
                if ($this->enabled) {
                    $this->config->enablingTime = time();
                }
                $this->config->write();
            }
        } else {
            $this->enabled = $this->config->enabled;
        }
    }

    public function clearCache()
    {
        $files = glob(APPLICATION_CACHE . '*');
        foreach ($files as $file) {
            @unlink($file);
        }
    }

    public function isCachingEnabled()
    {
        return $this->enabled;
    }
}