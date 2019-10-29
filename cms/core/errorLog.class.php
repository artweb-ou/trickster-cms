<?php

class errorLog
{
    /** @var errorLog */
    protected static $instance = null;
    protected $messageLogArray = [];
    protected $logFilePath = false;

    protected function __construct()
    {
        $todayDate = date('Y-m-d');
        $pathsManager = controller::getInstance()->getPathsManager();
        $this->logFilePath = $pathsManager->getPath('logs') . $todayDate . '.txt';
    }

    protected function writeLogContents($newMessage)
    {
        $myUrl = "unknown";
        $referer = "unknown";
        $ip = "unknown";

        if (!empty($_SERVER) && !empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
            // Get HTTP/HTTPS (the possible values for this vary from server to server)
            $myUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']), [
                    'off',
                    'no',
                ])) ? 'https' : 'http';
            // Get domain portion
            if (!empty($_SERVER['HTTP_HOST'])) {
                $myUrl .= '://' . $_SERVER['HTTP_HOST'];
            }
            // Get path to script
            if (!empty($_SERVER['REQUEST_URI'])) {
                $myUrl .= $_SERVER['REQUEST_URI'];
            }
            // Add path info, if any
            if (!empty($_SERVER['PATH_INFO'])) {
                $myUrl .= $_SERVER['PATH_INFO'];
            }
            // Add query string, if any (some servers include a ?, some don't)
            if (!empty($_SERVER['QUERY_STRING'])) {
                $myUrl .= '?' . ltrim($_SERVER['REQUEST_URI'], '?');
            }

            if (!empty($_SERVER['HTTP_REFERER'])) {
                $referer = $_SERVER['HTTP_REFERER'];
            }

            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }


        $contents = $newMessage['date'] . "\r\n"
            . "- " . $newMessage['locationName'] . ': ' . $newMessage['errorText'] . "\r\n"
            . "- REQUEST_URI: " . $myUrl . "\r\n"
            . "- IP: " . $ip . "\r\n"
            . "- HTTP_REFERER: " . $referer . "\r\n\r\n";

        $pathsManager = controller::getInstance()->getPathsManager();
        $pathsManager->ensureDirectory($pathsManager->getPath('logs'));
        file_put_contents($this->logFilePath, $contents, FILE_APPEND);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new errorLog();
        }
        return self::$instance;
    }

    public function getAllMessages()
    {
        return $this->messageLogArray;
    }

    public function logMessage($locationName, $errorText, $level = null, $throwException = true)
    {
        $newMessage = [];

        $newMessage['stamp'] = time();
        $newMessage['date'] = date('Y-m-d H:i:s', $newMessage['stamp']);
        $newMessage['locationName'] = $locationName;
        switch ($level) {
            case E_ERROR:
                $errorText = "[Error] " . $errorText;
                break;
            case E_WARNING:
                $errorText = "[Warning] " . $errorText;
                break;
            case E_NOTICE:
                $errorText = "[Notice] " . $errorText;
                break;
            default:
                $errorText = "[" . $level . "] " . $errorText;
                break;
        }
        $newMessage['errorText'] = $errorText;

        $this->writeLogContents($newMessage);
        if ($throwException && controller::getInstance()->getDebugMode()) {
            throw new Exception($errorText);
        }

    }
}