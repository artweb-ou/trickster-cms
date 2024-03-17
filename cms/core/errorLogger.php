<?php

class errorLogger
{
    protected function logError($message, $level = null, $throwException = true)
    {
        errorLog::getInstance()->logMessage($this->getErrorLogLocation(), $message, $level, $throwException);
    }

    protected function getErrorLogLocation()
    {
        return get_class($this);
    }
}