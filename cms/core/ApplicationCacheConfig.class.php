<?php

class ApplicationCacheConfig
{
    public $enabled = false;
    public $enablingTime = 0;
    protected $file = '';

    public function __construct($fileName)
    {
        $this->file = TEMPORARY_PATH . $fileName;
        if (is_file($this->file)) {
            $fileContents = file_get_contents($this->file);
            if ($fileContents) {
                $data = explode('|', $fileContents);
                if (count($data) == 2) {
                    $this->enabled = (bool)$data[0];
                    $this->enablingTime = (int)$data[1];
                }
            }
        } else {
            $this->write();
        }
    }

    public function write()
    {
        $tmpFile = $this->file . '.tmp';
        $data = (string)$this->enabled . '|' . (string)$this->enablingTime;
        $writeResult = file_put_contents($tmpFile, $data);

        if ($writeResult === false) {
            errorLog::getInstance()->logMessage(__CLASS__, 'Unable to write ' . $tmpFile);
        } else {
            rename($tmpFile, $this->file);
        }
    }
}