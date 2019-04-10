<?php

class post24ImportManager extends errorLogger
{
    const RESOURCE_URL = 'https://www.omniva.ee/locations.json';

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
