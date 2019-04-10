<?php

class newsletterApplication extends controllerApplication
{
    protected $applicationName = 'newsletter';
    public $rendererName = 'smarty';

    public function initialize()
    {
        $this->createRenderer();
    }

    public function execute($controller)
    {
        $structureManager = $this->getService('structureManager', [
            'rootUrl' => $controller->rootURL,
            'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerAdmin'),
        ], true);
        $structureManager->setPrivilegeChecking(false);

        if ($newsmailTextId = (int)($controller->getParameter('id'))) {
            if ($newsMailTextElement = $structureManager->getElementById($newsmailTextId)) {
                if ($data = $newsMailTextElement->getDispatchmentData()) {
                    $emailDispatcher = $this->getService('EmailDispatcher');
                    $newDispatchment = $emailDispatcher->getEmptyDispatchment();
                    $newDispatchment->setSubject($newsMailTextElement->title);
                    $newDispatchment->setData($data);
                    $newDispatchment->setReferenceId($newsMailTextElement->id);
                    $newDispatchment->setType($newsMailTextElement->getDispatchmentType());
                    echo $newDispatchment->getContent(true);
                }
                exit;
            }
        }
    }
}

