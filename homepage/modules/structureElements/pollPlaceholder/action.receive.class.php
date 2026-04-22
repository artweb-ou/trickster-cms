<?php

class receivePollPlaceholder extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param pollPlaceholderElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            $structureElement->persistDisplayMenusLinks();

            $linksManager = $this->getService(linksManager::class);

            $linksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'pollLink', 'parent');
            foreach ($linksIndex as $childId => &$link) {
                if ($childId != $structureElement->pollId) {
                    $link->delete();
                }
            }

            $linksManager->linkElements($structureElement->id, $structureElement->pollId, 'pollLink');

            $controller->redirect($structureElement->URL);
        }
        $structureElement->setViewName('form');
    }

    public function setValidators(&$validators)
    {
        $validators['pollId'][] = 'notEmpty';
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'pollId',
            'displayMenus',
        ];
    }
}

