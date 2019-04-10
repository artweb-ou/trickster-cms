<?php

class bannerApplication extends controllerApplication
{
    protected $applicationName = 'banner';
    protected $id = false;
    public $rendererName = 'smarty';

    public function initialize()
    {
        $this->startSession('public', $this->getService('ConfigManager')->get('main.publicSessionLifeTime'));
        $this->createRenderer();
    }

    public function execute($controller)
    {
        $this->processRequestParameters();
        if ($this->id) {
            $searchFields = ['id' => $this->id];
            $bannerCollection = persistableCollection::getInstance('module_banner');
            // TODO: find a way persist only single record (currently we have multiple records for i18n support)
            if ($dataObjects = $bannerCollection->load($searchFields, [], false, 2)) {
                $redirectUrl = '';
                foreach ($dataObjects as &$dataObject) {
                    $dataObject->clicks++;
                    $dataObject->persist();
                    if ($dataObject->link) {
                        $redirectUrl = $dataObject->link;
                    }
                }
                if (!$redirectUrl) {
                    $redirectUrl = $controller->baseURL;
                }
                $controller->redirect(html_entity_decode($redirectUrl, ENT_QUOTES, 'UTF-8'));
            }
        }
    }

    public function processRequestParameters()
    {
        $controller = controller::getInstance();
        if ($controller->getParameter('id')) {
            $this->id = $controller->getParameter('id');
        }
    }
}

