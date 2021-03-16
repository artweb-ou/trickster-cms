<?php
define("QUERY_PARAMETERS_SEPARATOR", ':');

class controller
{
    /**
     * @var controllerApplication
     */
    protected $application;
    protected $urlApplicationName;
    protected $applicationName;
    protected $defaultAplicationName = 'public';

    protected $requestParameters = [];
    public $requestURI = [];
    public $requestedPath = [];
    public $requestedFile = false;
    protected $protocol;
    public $domainName;
    public $directoryName;
    public $scriptName;
    public $domainURL;
    public $baseURL;
    public $rootURL;
    public $pathURL;
    public $fullURL;
    public $visitorIP;
    public $fullParametersURL;
    /**
     * @var ConfigManager
     */
    public $configManager;

    protected $formData = [];
    protected $forceDebug = false;
    protected $debugMode = null;
    /** @var controller */
    private static $instance;
    protected $enabledPlugins = [];
    /**
     * @var PathsManager
     */
    protected $pathsManager;
    public $redirectDeprecatedParameters = false;

    /**
     * @param null $configurationFile
     * @return controller
     */
    public static function getInstance($configurationFile = null)
    {
        if (is_null(self::$instance)) {
            //sometimes during controller::_construct instance is asked already twice, so we have to make it instantly not null
            self::$instance = false;
            self::$instance = new controller($configurationFile);
        }
        return self::$instance;
    }

    protected function __construct($projectConfigPath)
    {
        ob_start();
        $corePath = dirname(__FILE__) . '/';
        include_once($corePath . "PathsManager.class.php");
        include_once($corePath . "ConfigManager.class.php");
        include_once($corePath . "Config.class.php");
        include_once($corePath . "AutoLoadManager.php");
        $this->configManager = new ConfigManager();
        $this->pathsManager = new PathsManager();
        if ($projectPathsConfig = $this->configManager->getConfigFromPath($projectConfigPath . 'paths.php')) {
            if ($tricksterPath = $projectPathsConfig->get('trickster')) {
                if ($projectMainConfig = $this->configManager->getConfigFromPath($projectConfigPath . 'main.php')) {
                    if ($plugins = $projectMainConfig->get('enabledPlugins')) {
                        foreach (array_reverse($plugins) as $key => $dir) {
                            if ($key === 'project') {
                                $this->configManager->addSource(ROOT_PATH . $dir . 'config/', true);
                            } else {
                                $this->configManager->addSource(ROOT_PATH . $tricksterPath . $dir . 'config/');
                            }
                        }
                    }
                }
            }
        }


        $mainConfig = $this->configManager->getConfig('main');
        $pathsConfig = $this->configManager->getConfig('paths');
        $this->pathsManager->setConfig($pathsConfig);

        $composerClassLoaderPath = ROOT_PATH . $pathsConfig->get('psr0Classes') . 'autoload.php';
        if (is_file($composerClassLoaderPath)) {
            include_once($composerClassLoaderPath);
        }
        //autoloadmanager should be loaded after composer's autoload to be included into beginning of autoloaders stack
        new AutoLoadManager();

        $this->checkPlugins();
        ini_set("log_errors_max_len", 0);
        ini_set("pcre.backtrack_limit", 10000000);
        ini_set("memory_limit", "2048M");
        ini_set("max_execution_time", "30");

        //log all errors, but never display them
        set_error_handler([$this, 'catchError']);
        register_shutdown_function([$this, 'exitHandler']);
        if ($reporting = $mainConfig->get('errorReporting')) {
            if (is_string($reporting)) {
                ini_set("error_reporting", eval('return ' . $reporting . ';'));
            } elseif (is_numeric($reporting)) {
                ini_set("error_reporting", $reporting);
            }
        }
        ini_set("display_errors", 0);

        mb_internal_encoding("UTF-8");
        date_default_timezone_set($mainConfig->get('timeZone'));
        $this->parseRequestParameters();
    }

    protected function checkPlugins()
    {
        if ($enabledPluginsInfo = $this->configManager->get('main.enabledPlugins')) {
            $this->enabledPlugins = array_keys($enabledPluginsInfo);
            foreach ($enabledPluginsInfo as $pluginName => $pluginPath) {
                if ($pluginName == 'project') {
                    $this->addIncludePath(ROOT_PATH . $pluginPath);
                } else {
                    $this->addIncludePath(ROOT_PATH . $this->pathsManager->getRelativePath('trickster') . $pluginPath);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getEnabledPlugins()
    {
        return $this->enabledPlugins;
    }

    protected function parseRequestParameters()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            $this->domainName = $_SERVER['HTTP_HOST'];
        }
        if ($this->isSsl()) {
            $this->protocol = 'https://';
        } else {
            $this->protocol = 'http://';
        }
        $this->domainURL = $this->configManager->get('main.protocol') . $this->domainName;
        $this->directoryName = trim(trim(dirname($_SERVER['SCRIPT_NAME']), '/'), '\\');
        $this->scriptName = basename($_SERVER['SCRIPT_NAME']);
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $this->visitorIP = $_SERVER['REMOTE_ADDR'];
        }

        //get the request array
        if (!empty($_SERVER["REQUEST_URI"])) {
            $this->requestURI = $this->parseRequestURI($_SERVER["REQUEST_URI"]);
        }

        $this->detectApplication();
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        $this->domainURL = $this->protocol . $this->domainName;
    }

    protected function isSsl()
    {
        if (isset($_SERVER['HTTPS'])) {
            if ('on' == strtolower($_SERVER['HTTPS'])) {
                return true;
            }
            if ('1' == $_SERVER['HTTPS']) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }

    public function getApplication()
    {
        if ($this->application === null) {
            $this->requestedPath = $this->requestURI;
            $this->parseFormData();
            $this->detectFileName();

            $className = $this->applicationName . 'Application';
            $this->application = new $className($this, $this->applicationName);
            $this->requestParameters = $this->findRequestParameters($this->requestedPath);
            $result = $this->application->initialize();
            if ($result === false) {
                $this->application = false;
            } else {
                $this->urlApplicationName = $this->application->getUrlName();
            }

            if ($this->application) {
                if ($this->directoryName != '' && $this->directoryName != '/') {
                    $this->baseURL = $this->domainURL . '/' . $this->directoryName . '/';
                } else {
                    $this->baseURL = $this->domainURL . '/';
                }

                if ($this->urlApplicationName) {
                    $this->rootURL = $this->baseURL . $this->urlApplicationName . '/';
                } else {
                    $this->rootURL = $this->baseURL;
                }
                if ($this->requestedPath) {
                    $this->pathURL = $this->rootURL . implode('/', $this->requestedPath) . '/';
                } else {
                    $this->pathURL = $this->rootURL;
                }
                if ($this->requestURI) {
                    $this->fullURL = $this->rootURL . implode('/', $this->requestURI) . '/';
                } else {
                    $this->fullURL = $this->rootURL;
                }

                if ($imploded = $this->getParametersString(true)) {
                    $this->fullParametersURL = $this->fullURL . $imploded;
                } else {
                    $this->fullParametersURL = $this->fullURL;
                }
            }
            if ($this->redirectDeprecatedParameters) {
                if (empty($this->requestParameters['filename'])) {
                    $cssFileName = $this->requestedFile;
                } else {
                    $cssFileName = '';
                }
                $this->redirect($this->baseURL . $this->urlApplicationName . '/' . $this->getParametersString(true) . $cssFileName, '301');
            }
        }
        return $this->application;
    }

    public function dispatch()
    {
        try {
            if ($application = $this->getApplication()) {
                if ($this->application instanceof ApplicationCacheInterface && $this->application->canServeCache()) {
                    return $this->application->serveCache();
                } else {
                    return $this->application->execute($this);
                }
            } else {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
        } catch (Exception $exception) {
            errorLog::getInstance()->logMessage('controller', $exception->getMessage() . "\n". $exception->getTraceAsString());
        }
    }

    public function getDebugMode()
    {
        if ($this->debugMode === null) {
            if ($this->forceDebug) {
                $this->debugMode = true;
            } elseif (strstr($this->domainName, 'localhost') || strstr($this->domainName, '.local') || strstr($this->domainName, '.loc') || !strstr($this->domainName, '.')) {
                $this->debugMode = true;
            } else {
                $this->debugMode = false;
            }
        }
        return $this->debugMode;
    }

    public function getParametersString($encoded = false)
    {
        $imploded = "";
        foreach ($this->requestParameters as $key => $value) {
            if (!is_array($value)) {
                if ($encoded) {
                    $imploded .= $key . ":" . urlencode($value) . "/";
                } else {
                    $imploded .= $key . ":" . $value . "/";
                }
            }
        }
        return $imploded;
    }

    protected function detectFileName()
    {
        if ($this->requestedPath) {
            $lastElement = end($this->requestedPath);
            if (stripos($lastElement, '.') !== false && stripos($lastElement, ':') === false) {
                $this->requestedFile = $lastElement;
            }
        }
    }

    protected function detectApplication()
    {
        if ($this->requestURI) {
            $applicationName = reset($this->requestURI);
            $fileDirectory = $this->pathsManager->getRelativePath('applications');
            if ($fileName = $this->pathsManager->getIncludeFilePath($fileDirectory . $applicationName . '.class.php')) {
                $this->applicationName = $applicationName;
                array_shift($this->requestURI);
            }
        }
        if (!$this->applicationName) {
            $this->applicationName = $this->defaultAplicationName;
        }
    }

    protected function findRequestParameters(&$requestURI)
    {
        //fill found parameters with $_GET and $_POST values.
        //$_REQUEST is not needed, because it contains cookies
        //POST has higher priority
        $foundParameters = [];
        if (isset($_POST)) {
            $foundParameters += $_POST;
        }
        if (isset($_GET)) {
            $foundParameters += $_GET;
        }
        //search for parameters divided with standard separator (colon?)
        foreach ($requestURI as $key => &$requestURIPart) {
            if (strpos($requestURIPart, QUERY_PARAMETERS_SEPARATOR) !== false) {
                $strings = explode(QUERY_PARAMETERS_SEPARATOR, $requestURIPart);
                if (!isset($foundParameters[$strings[0]])) {
                    $foundParameters[$strings[0]] = $strings[1];
                }
                unset($requestURI[$key]);
            }
        }

        return $foundParameters;
    }

    protected function parseRequestURI($requestURI)
    {
        $requestString = urldecode($requestURI);

        //strip current subdirectory from request string if working not from the root directory on server
        if ($this->directoryName != '') {
            if (strpos($requestURI, $this->directoryName) === 0) {
                $requestString = substr($requestString, strlen($this->directoryName));
            } else {
                if (strpos($requestURI, '/' . $this->directoryName) === 0) {
                    $requestString = substr($requestString, strlen('/' . $this->directoryName));
                }
            }
        }

        //strip 'index.php' from request string if there was one
        $requestString = str_replace($this->scriptName, '', $requestString);

        //strip all GET parameters
        if ($position = strpos($requestString, '?')) {
            $requestString = substr_replace($requestString, '', $position);
        }

        //clean request string from possibly empty elements
        $requestArray = explode('/', $requestString);
        foreach ($requestArray as $key => &$name) {
            if (strlen(trim($name)) == 0) {
                unset($requestArray[$key]);
            }
        }
        return array_values($requestArray);
    }

    public function restart($newURL = null)
    {
        if (is_null($newURL)) {
            $newURL = $this->fullURL;
        }

        if ($this->domainURL != '') {
            if (strpos($newURL, $this->domainURL) === 0) {
                $newURL = substr($newURL, strlen($this->domainURL));
            }
        }
        if ($this->directoryName != '') {
            if (strpos($newURL, $this->directoryName) === 0) {
                $newURL = substr($newURL, strlen($this->directoryName));
            }
        }

        $_SERVER["REQUEST_URI"] = $newURL;

        $_POST = [];
        $_FILES = [];
        $_GET = [];

        $this->parseRequestParameters();
        $this->application = null;
        $this->dispatch();

        exit();
    }

    public function redirect($newURL, $statusCode = '302')
    {
        if (is_null($newURL)) {
            $newURL = $this->fullURL;
        }
        $httpResponse = CmsHttpResponse::getInstance();
        $httpResponse->setStatusCode($statusCode);
        $httpResponse->setLocation($newURL);
        $httpResponse->sendHeaders();
        exit();
    }

    protected function parseFormData()
    {
        $formData = [];
        if (isset($_FILES['formData'])) {
            foreach ($_FILES['formData'] as $fileProperty => $elementsList) {
                foreach ($elementsList as $elementId => $elementData) {
                    foreach ($elementData as $propertyName => $propertyValue) {
                        if (is_array($propertyValue)) {
                            $languageId = $propertyName;
                            foreach ($propertyValue as $fieldName => &$fieldValue) {
                                $formData[$elementId][$languageId][$fieldName][$fileProperty] = $fieldValue;
                            }
                        } else {
                            $fieldName = $propertyName;
                            $fieldValue = $propertyValue;
                            $formData[$elementId][$fieldName][$fileProperty] = $fieldValue;
                        }
                    }
                }
            }
        }
        if (isset($_POST['formData'])) {
            foreach ($_POST['formData'] as $elementId => $elementData) {
                foreach ($elementData as $fieldName => $fieldValue) {
                    if (isset($formData[$elementId][$fieldName]) && is_array($formData[$elementId][$fieldName])) {
                        $formData[$elementId][$fieldName] = array_merge($formData[$elementId][$fieldName], $fieldValue);
                    } else {
                        $formData[$elementId][$fieldName] = $fieldValue;
                    }
                }
            }
        }

        $this->formData = $formData;
    }

    public function getElementFormData($elementId)
    {
        if (!is_numeric($elementId)) {
            foreach ($this->formData as $key => &$data) {
                if (!is_numeric($key)) {
                    return $data;
                }
            }
        } elseif (isset($this->formData[$elementId])) {
            return $this->formData[$elementId];
        }
        return false;
    }

    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;
    }

    public function getApplicationName()
    {
        if (!$this->applicationName) {
            $this->detectApplication();
        }
        return $this->applicationName;
    }

    public function setDirectoryName($directoryName)
    {
        $this->directoryName = $directoryName;
    }

    //deprecated

    /**
     * @param $parameterName
     * @return bool
     *
     * todo: remove after 2016
     * @deprecated
     */
    public function getRequestParameterValue($parameterName)
    {
        errorLog::getInstance()->logMessage('controller', "Deprecated method used: getRequestParameterValue");
        return $this->getParameter($parameterName);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->requestParameters;
    }

    /**
     * @param $parameterName
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        $value = false;
        if (isset($this->requestParameters[$parameterName])) {
            $value = $this->requestParameters[$parameterName];
        }
        return $value;
    }

    /**
     * @param string $includePath
     */
    public function addIncludePath($includePath)
    {
        $this->pathsManager->addIncludePath($includePath);
    }

    /**
     * @param $projectPath
     * @deprecated
     */
    public function setProjectPath($projectPath)
    {
        $this->addIncludePath($projectPath);
    }

    /**
     * @deprecated
     */
    public function getProjectPath()
    {
        $includePaths = $this->pathsManager->getIncludePaths();
        return end($includePaths);
    }

    /**
     * @return mixed
     * @deprecated since 04 2016
     */
    public function getIncludePaths()
    {
        return $this->pathsManager->getIncludePaths();
    }

    /**
     * @param $filePath
     * @return bool|string
     * @deprecated since 04 2016, use PathsManager
     */
    public function getIncludeFilePath($filePath)
    {
        return $this->pathsManager->getIncludeFilePath($filePath);
    }

    public function catchError($level, $message, $file, $line)
    {
        $currentErrorLevel = error_reporting();
        if ($currentErrorLevel & $level) {
            errorLog::getInstance()->logMessage($file . ":" . $line, $message, $level);
        }
        // Don't execute PHP internal error handler
        return true;
    }

    public function exitHandler()
    {
        if ($error = error_get_last()) {
            $this->catchError($error["type"], $error["message"], $error["file"], $error["line"]);
        }
    }

    public function setApplication($applicationName)
    {
        $this->applicationName = $applicationName;
    }

    /**
     * @return array
     */
    public function getRequestedPath()
    {
        return $this->requestedPath;
    }

    public function getConfigManager()
    {
        return $this->configManager;
    }

    public function getPathsManager()
    {
        return $this->pathsManager;
    }

    public function getPathTo($pathName)
    {
        $result = '';
        if ($path = $this->configManager->get("paths.$pathName")) {
            $result = ROOT_PATH . $path;
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}