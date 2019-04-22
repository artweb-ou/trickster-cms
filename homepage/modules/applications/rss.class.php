<?php

class rssApplication extends controllerApplication
{
    protected $applicationName = '';
    public $rendererName = 'smarty';
    public $themeCode = '';
    /**
     * @var Config
     */
    public $config;

    public function initialize()
    {
        $configManager = $this->getService('ConfigManager');
        $this->themeCode = $configManager->get('main.rssTheme');
        $this->startSession('public', $configManager->get('main.publicSessionLifeTime'));
        $this->config = $configManager->getConfig('rss');
        $this->createRenderer();
    }

    public function execute($controller)
    {
        /**
         * @var Cache $cache
         */
        $cache = $this->getService('Cache');
        $cache->enable();

        $configManager = $this->getService('ConfigManager');
        /**
         * @var structureManager $structureManager
         */
        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->baseURL,
            'rootMarker' => $configManager->get('main.rootMarkerPublic'),
        ], true);

        $languagesManager = $this->getService('languagesManager');
        $languageId = $languagesManager->getCurrentLanguageId();

        $structureManager->getElementById($languageId);

        $limit = 100;
        $rssItems = [];
        if ($types = $this->config->getMerged('types')) {
            if ($elements = $structureManager->getElementsByType($types, $languageId, ['dateCreated' => '0'], $limit)
            ) {
                foreach ($elements as &$element) {
                    if (!$element->hidden && ($element->structureType != 'product' || ($element->inactive == '0' && $element->isPurchasable()))
                    ) {
                        $rssItems[] = $element;
                    }
                }
            }
        }

        $dates = [];
        foreach ($rssItems as &$item) {
            $timeStamp = strtotime($item->dateCreated);
            $item->rssDate = date(DATE_RFC822, $timeStamp);
            $item->guid = md5($item->guid . $item->rssDate);
            $item->setTemplate('rss.' . $item->structureType . '.tpl');

            $dates[] = $timeStamp;
        }

        array_multisort($dates, SORT_DESC, $rssItems);
        $designThemesManager = $this->getService('DesignThemesManager', ['currentThemeCode' => $this->getThemeCode()], true);
        $currentTheme = $designThemesManager->getCurrentTheme();

        $settingsManager = $this->getService('settingsManager');
        $settings = $settingsManager->getSettingsList($this->getService('languagesManager')->getCurrentLanguageId());
        $this->renderer->assign('settings', $settings);
        $this->renderer->assign('controller', $controller);
        $this->renderer->assign('rssItems', $rssItems);
        $this->renderer->setContentDisposition('inline');
        $this->renderer->setContentType('application/rss+xml');
        $this->renderer->assign('theme', $currentTheme);

        $this->renderer->setCacheControl('no-cache');
        $this->renderer->template = $currentTheme->template('rss.index.tpl');

        $this->renderer->display();
    }

    public function getUrlName()
    {
        return '';
    }

    public function getThemeCode()
    {
        return $this->themeCode;
    }
}

