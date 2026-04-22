<?php

class previewNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param newsMailsTextElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('form');
        if ($structureElement->hasActualStructureInfo()) {
            $designThemesManager = $this->getService(DesignThemesManager::class);
            $configManager = $this->getService(ConfigManager::class);
            $designThemesManager->setCurrentThemeCode($configManager->get('main.publicTheme'));
            $structureManager = $this->getService('structureManager');
            $emailDispatcher = $this->getService(EmailDispatcher::class);
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
