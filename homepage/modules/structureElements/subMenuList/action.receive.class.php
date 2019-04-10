<?php

class receiveSubMenuList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $linksManager = $this->getService('linksManager');
            $linksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'submenulist', 'parent');
            foreach ($structureElement->menus as $menuId) {
                $linksManager->linkElements($structureElement->id, $menuId, 'submenulist', true);
                unset($linksIndex[$menuId]);
            }
            foreach ($linksIndex as &$link) {
                $link->delete();
            }

            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();

            $structureElement->persistDisplayMenusLinks();

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'type',
            'menus',
            'displayMenus',
            'maxLevels',
            'skipLevels',
            'levels',
            'popup',
            'displayHeadingAutomatically',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}