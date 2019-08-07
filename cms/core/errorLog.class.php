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

    public function __destruct()
    {
        $this->writeLogContents();
    }

    protected function writeLogContents()
    {
        if (count($this->messageLogArray)) {
            $contents = $this->getLogContents();

            $use_server = true;
            $required_server_variables = ["HTTP_HOST", "REQUEST_URI"];
            foreach ($required_server_variables as $var) {
                if (empty($_SERVER[$var])) {
                    $use_server = false;
                }
            }
            $myUrl = "unknown";
            if ($use_server) {
                // Get HTTP/HTTPS (the possible values for this vary from server to server)
                $myUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']), [
                        'off',
                        'no',
                    ])) ? 'https' : 'http';
                // Get domain portion
                $myUrl .= '://' . $_SERVER['HTTP_HOST'];
                // Get path to script
                $myUrl .= $_SERVER['REQUEST_URI'];
                // Add path info, if any
                if (!empty($_SERVER['PATH_INFO'])) {
                    $myUrl .= $_SERVER['PATH_INFO'];
                }
                // Add query string, if any (some servers include a ?, some don't)
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $myUrl .= '?' . ltrim($_SERVER['REQUEST_URI'], '?');
                }
            }

            $referer = "unknown";
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $referer = $_SERVER['HTTP_REFERER'];
            }
            $ip = "";
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            foreach ($this->messageLogArray as &$item) {
                $contents .= $item['date'] . "\n\r"
                    . "- " . $item['locationName'] . ': ' . $item['errorText'] . "\n\r"
                    . "- REQUEST_URI: " . $myUrl . "\n\r"
                    . "- IP: " . $ip . "\n\r"
                    . "- HTTP_REFERER: " . $referer . "\n\r\n\r";
            }
            $pathsManager = controller::getInstance()->getPathsManager();
            $pathsManager->ensureDirectory($pathsManager->getPath('logs'));
            file_put_contents($this->logFilePath, $contents);
            $this->messageLogArray = [];
        }
    }

    protected function getLogContents()
    {
        if (file_exists($this->logFilePath)) {
            return file_get_contents($this->logFilePath);
        } else {
            $empty = '';
            return $empty;
        }
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
        $newMessage['date'] = date('Y-m-d H:i', $newMessage['stamp']);
        $newMessage['locationName'] = $locationName;
        if ($level) {
            $newMessage['errorText'] = "[" . $level . "] " . $errorText;
        } else {
            $newMessage['errorText'] = $errorText;
        }
        $this->messageLogArray[] = $newMessage;

        if ($level == E_ERROR || $level == E_COMPILE_ERROR || $level == E_CORE_ERROR) {
            //__destruct is not guaranteed to happen after E_ERROR
            $this->writeLogContents();
        }
        if ($throwException && controller::getInstance()->getDebugMode()) {
            throw new Exception($errorText);
        }
    }
}