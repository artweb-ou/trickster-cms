<?php

class smartPostImportManager extends errorLogger
{
    const RESOURCE_URL = 'http://www.smartpost.ee/places.js';

    public function getPostAutomates()
    {
        return $this->fetchInfo();
    }

    protected function fetchInfo()
    {
        $result = '';
        if (ini_get('allow_url_fopen')) {
            $result = file_get_contents(self::RESOURCE_URL);
        } else {
            $this->logError('Download failure - allow_url_fopen=false');
        }
        return $result;
    }
}
