<?php

class previewNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('form');
        if ($structureElement->hasActualStructureInfo()) {
            $designThemesManager = $this->getService('designThemesManager', [], true);
            $configManager = $this->getService('ConfigManager');
            $designThemesManager->setCurrentThemeCode($configManager->get('main.publicTheme'));
            $structureManager = $this->getService('structureManager');
            $emailDispatcher = $this->getService('EmailDispatcher');
            $newDispatchment = $emailDispatcher->getEmptyDispatchment();
            $newDispatchment->setFromName($structureElement->from);
            $newDispatchment->setFromEmail($structureElement->fromEmail);
            $newDispatchment->setSubject($structureElement->title);
            $newDispatchment->setData($structureElement->getDispatchmentData());
            $newDispatchment->setReferenceId($structureElement->id);
            $newDispatchment->setType($structureElement->getDispatchmentType());
            echo $newDispatchment->getContent(true);
            exit;
        }
    }
}
