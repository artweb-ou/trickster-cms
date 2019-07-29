<?php

/**
 * Controller application is standardized script, which purpose is to receive external parameters (whether from GET/POST or other objects), operate some business logic according to them and optionally provide some rendered answer
 */
abstract class controllerApplication extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    /**
     * @var string - used in URL building by default
     */
    protected $applicationName;
    /**
     * @var string - the name of renderer plugin used by application
     */
    public $rendererName;
    /**
     * @var rendererPlugin
     */
    public $renderer;
    /**
     * @var controller
     */
    protected $controller;

    public function __construct(controller $controller, $applicationName)
    {
        $this->controller = $controller;
        $this->applicationName = $applicationName;
        $pathsManager = $controller->getPathsManager();
        $paths = $pathsManager->getIncludePaths();
        $servicesFolder = $pathsManager->getRelativePath('services');
        foreach ($paths as &$path) {
            $path .= $servicesFolder;
        }
        $registry = new DependencyInjectionServicesRegistry($paths);
        $this->setRegistry($registry);
        //global registry is used for non-updated singleton classes.
        DependencyInjectionContextTrait::setGlobalRegistry($registry);
        $this->setService('controllerApplication', $this);
        //temporary workaround for renderer object. Remove after "renderers" architecture change
        if ($factory = renderer::getFactory()) {
            $this->instantiateContext($factory);
        }
        $this->setService('controller', $this->controller);
        $this->setService('ConfigManager', $this->controller->getConfigManager());
        $this->setService('PathsManager', $pathsManager);
    }

    /**
     * Start session under specified name
     *
     * @param string $sessionName
     * @param $lifeTime
     */
    protected function startSession($sessionName = 'default', $lifeTime = false)
    {
        /**
         * @var $sessionManager ServerSessionManager
         */
        $sessionManager = $this->getService('ServerSessionManager', ['sessionName' => $sessionName]);
        $sessionManager->setEnabled(true);
        if ($lifeTime) {
            $sessionManager->setSessionLifeTime($lifeTime);
        }
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Creates renderer plugin object for specified renderer name
     */
    protected function createRenderer()
    {
        if ($this->renderer = $this->getService('renderer', ['name' => $this->rendererName], true)) {
            $controller = controller::getInstance();
            $this->renderer->debugMode = $controller->getDebugMode();
        }
    }

    /**
     * Every application can specify its name used in URL building. It's name can also be empty if needed to omit.
     *
     * @return string|null
     */
    public function getUrlName()
    {
        return $this->applicationName;
    }

    public function getApplicationName()
    {
        return $this->applicationName;
    }

    public function getRequestUrl()
    {
        return controller::getInstance()->fullParametersURL;
    }

    public function getParameter($parameter)
    {
        return $this->controller->getParameter($parameter);
    }

    public function getUser()
    {
        return $this->getService('user');
    }

    /**
     * Always called before execution of application
     *
     * @abstract
     * @return boolean
     */
    abstract public function initialize();

    /**
     * Starts execution of application main logic, usually echos some rendered content as well through renderer plugin
     *
     * @abstract
     * @param controller $controller
     * @return mixed
     */
    abstract public function execute($controller);

    public function deprecatedParametersRedirection()
    {
        return false;
    }

    public function getDesignThemesManager()
    {
        return $this->getService('DesignThemesManager');
    }

    public function getLanguagesManager()
    {
        return $this->getService('LanguagesManager');
    }
}
