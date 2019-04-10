<?php

class errorLogger
{
    protected function logError($message)
    {
        errorLog::getInstance()->logMessage($this->getErrorLogLocation(), $message);
    }

    protected function getErrorLogLocation()
    {
        return get_class($this);
    }
}