<?php

class adminApplication extends controllerApplication implements ThemeCodeProviderInterface
{
    protected $applicationName = 'admin';
    /**
     * @var DesignTheme
     */
    protected $currentTheme;
    protected $themeCode = 'projectAdmin';
    public $rendererName = 'smarty';

    public function initialize()
    {
        $this->startSession('admin', $this->getService('ConfigManager')->get('main.adminSessionLifeTime'));
        $this->createRenderer();
    }

    /**
     * @param controller $controller
     * @return mixed|void
     */
    public function execute($controller)
    {
        /**
         * @var Cache $cache
         */
        $cache = $this->getService('Cache');
        $cache->enable(false, false, true);

        /**
         * @var $redirectionManager RedirectionManager
         */
        $redirectionManager = $this->getService('RedirectionManager');
        $redirectionManager->checkProtocolRedirection();
        $redirectionManager->checkDomainRedirection();
        /**
         * @var DesignThemesManager $designThemesManager
         */
        $designThemesManager = $this->getService('DesignThemesManager', ['currentThemeCode' => $this->getThemeCode()]);
        $currentTheme = $this->currentTheme = $designThemesManager->getCurrentTheme();

        $languagesManager = $this->getService('LanguagesManager');
        if ($langCode = $controller->getParameter('lang')) {
            $languagesManager->setCurrentLanguageCode($langCode, 'adminLanguages');
            //change the public language as well, so all public-languages dependent data in admin would be displayed in a same language where possible
            $languagesManager->setCurrentLanguageCode($langCode);
        }

        // list of languages for the language selector
        $languagesList = $languagesManager->getLanguagesList("adminLanguages");
        if (is_array($languagesList) && count($languagesList)) {
            $this->renderer->assign('languagesList', $languagesList);
        }

        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->rootURL,
            'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerAdmin'),
            'configActions' => true,
        ], true);
        $privilegesManager = $this->getService('privilegesManager');
        $this->processRequestParameters();

        $rootElement = $structureManager->getRootElement();
        $this->renderer->assign('leftMenu', [$rootElement]);
        if ($currentElement = $structureManager->getCurrentElement()) {
            $privileges = $privilegesManager->getElementPrivileges($currentElement->id);
            $currentElementPrivileges = $privileges[$currentElement->structureType];
            $rootPrivileges = $privileges['root'];
            $this->renderer->assign('currentElementPrivileges', $currentElementPrivileges);
            $this->renderer->assign('rootPrivileges', $rootPrivileges);
            $this->renderer->assign('privileges', $privileges);
            $this->renderer->assign('privilegesManager', $privilegesManager);
        }
        $this->renderer->assign('currentElement', $currentElement);

        $translationsManager = $this->getService(translationsManager::class);
        $translationsManager->setDefaultSection('adminTranslations');
        $translationsList = $translationsManager->getGroupTranslationsList('calendar', 'public_translations');
        $translationsList = array_merge($translationsList, $translationsManager->getTranslationsList('adminTranslations'));
        $this->renderer->assign('translationsList', $translationsList);

        $breadcrumbsManager = $this->getService('breadcrumbsManager');
        $currentLocation = $breadcrumbsManager->getBreadcrumbs(false, false, false);

        $user = $this->getService('user');
        if ($userElement = $structureManager->getElementById($user->id)) {
            $this->renderer->assign('userElement', $userElement);
        }

        $currentLanguage = $languagesManager->getCurrentLanguage("adminLanguages");
        $this->renderer->assign('currentLanguage', $currentLanguage);
        $this->renderer->assign('currentLanguageId', $currentLanguage->id);
        $this->renderer->assign('currentLocation', $currentLocation);

        $this->renderer->assign('theme', $currentTheme);
        $this->renderer->assign('rootElement', $rootElement);
        $this->renderer->assign('controller', $controller);
        $this->renderer->assign('user', $user);
        $this->renderer->assign('settings', $this->getService('settingsManager')->getSettingsList());

        $allowedSearchTypes = $this->getService('ConfigManager')->getMerged('searchtypes-admin.search');
        $this->renderer->assign('allowedSearchTypes', implode(',', $allowedSearchTypes));

        $resourcesUniterHelper = $this->getService('ResourcesUniterHelper', ['currentThemeCode' => $currentTheme->getCode()]);
        $this->renderer->assign('JSFileName', $this->getJsScripts($resourcesUniterHelper));
        $this->renderer->assign('CSSFileName', $resourcesUniterHelper->getResourceCacheFileName('css'));

        $this->renderer->assign('currentFullUrl', $this->getCurrentFullUrl());

        $this->renderer->template = $currentTheme->template('index.tpl');
        $this->renderer->setCacheControl('no-cache');
        $this->renderer->setContentType('text/html');
        $this->renderer->display();
    }

    public function processRequestParameters()
    {
        $structureManager = $this->getService('structureManager');
        $controller = controller::getInstance();
        if ($action = $controller->getParameter('action')) {
            if ($type = $controller->getParameter('type')) {
                $controller = controller::getInstance();
                if (count($controller->requestedPath)) {
                    $requestedPath = implode('/', $controller->requestedPath) . '/';
                } else {
                    $requestedPath = '';
                }
                $structureManager->newElementParameters[$requestedPath]['action'] = $action;
                $structureManager->newElementParameters[$requestedPath]['type'] = $type;

                if ($controller->getParameter('linkType')) {
                    $structureManager->setNewElementLinkType($controller->getParameter('linkType'));
                }
            } elseif ($id = $controller->getParameter('id')) {
                $structureManager->customActions[$id] = $action;
            }
        }
    }

    public function getThemeCode()
    {
        return $this->themeCode;
    }

    /**
     * @return DesignTheme
     */
    public function getCurrentTheme()
    {
        return $this->currentTheme;
    }

    /**
     * @param $currentElement
     * @return array
     */
    protected function getJsScripts($currentElement)
    {
        $controller = controller::getInstance();
        $jsScripts = [];
        if ($controller->getDebugMode()) {
            foreach ($this->currentTheme->getJavascriptResources() as $resource) {
                $jsScripts[] = $resource['fileUrl'] . $resource['fileName'];
            };
        } else {
            $resourcesUniterHelper = $this->getService('ResourcesUniterHelper');
            $jsScripts[] = $controller->baseURL . 'javascript/set:' . $this->currentTheme->getCode() . '/file:' . $resourcesUniterHelper->getResourceCacheFileName('js') . '.js';
        }
        if ($currentElement instanceof clientScriptsProviderInterface
        ) {
            $jsScripts = array_merge($jsScripts, $currentElement->getClientScripts());
        }
        return $jsScripts;
    }

    /**
     * @return string
     */
    protected function getCurrentFullUrl()
    {
        $excludedParameters = ['lang'];
        $controller = controller::getInstance();
        $parameters = $controller->getParameters();

        $structureManager = $this->getService('structureManager');
        $currentElement = ($structureManager->getCurrentElement()) ? : $structureManager->getRootElement();

        $fullUrl = new UrlBuilder();

        return $fullUrl->getUrlParametersString($parameters,$currentElement->URL, $excludedParameters);
    }
}