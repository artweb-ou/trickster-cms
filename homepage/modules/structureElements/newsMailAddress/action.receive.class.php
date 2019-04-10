<?php

class receiveNewsMailAddress extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->email;
            $structureElement->persistElementData();

            $compiledLinks = [];
            if ($elementLinks = $linksManager->getElementsLinks($structureElement->id, 'newsmailGroup', 'child')) {
                foreach ($elementLinks as &$link) {
                    $groupId = $link->parentStructureId;
                    $compiledLinks[$groupId] = $link;
                }
            }
            $groupsFolder = $structureManager->getElementByMarker('newsMailsGroups');
            $groupsList = $structureManager->getElementsChildren($groupsFolder->id, 'content');

            foreach ($groupsList as &$group) {
                if (isset($compiledLinks[$group->id]) && !in_array($group->id, $structureElement->groups)) {
                    $compiledLinks[$group->id]->delete();
                } elseif (!isset($compiledLinks[$group->id]) && in_array($group->id, $structureElement->groups)) {
                    $linksManager->linkElements($group->id, $structureElement->id, 'newsmailGroup');
                }
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['personalName', 'email', 'groups'];
    }

    public function setValidators(&$validators)
    {
        $validators['email'][] = 'notEmpty';
        $validators['email'][] = 'email';
    }
}

