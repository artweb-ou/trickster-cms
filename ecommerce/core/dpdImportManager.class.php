<?php

class dpdImportManager extends errorLogger
{
    public function __construct()
    {
    }

    public function getInfo()
    {
        $result = false;

        if ($list = $this->loadInfo()) {
            if ($list->Error_code == 0) {
                $result = $list->data;
            } else {
                $this->logError('JSON error received: ' . $list->Error_message);
            }
        }
        return $result;
    }

    protected function loadInfo()
    {
        $jsonInfo = false;

        try {
            if ($contents = file_get_contents("http://www.pakivedu.ee/rpc/gateway?op=pudo")) {
                $jsonInfo = json_decode($contents);
            }
        } catch (Exception $e) {
        }
        if (!$jsonInfo) {
            $this->logError('JSON query problem');
        }
        return $jsonInfo;
    }
}
