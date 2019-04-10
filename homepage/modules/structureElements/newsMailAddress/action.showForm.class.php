<?php

class showFormNewsMailAddress extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            //$structureElement->setViewName('form');
            $linksManager = $this->getService('linksManager');
            $compiledLinks = [];
            if ($elementLinks = $linksManager->getElementsLinks($structureElement->id, 'newsmailGroup', 'child')) {
                foreach ($elementLinks as &$link) {
                    $groupId = $link->parentStructureId;
                    $compiledLinks[$groupId] = $link;
                }
            }

            if ($groupsFolder = $structureManager->getElementByMarker('newsMailsGroups')) {
                $groupsList = $structureManager->getElementsChildren($groupsFolder->id, 'content');

                $structureElement->groupsList = [];
                foreach ($groupsList as &$group) {
                    $groupItem = [];
                    $groupItem['select'] = isset($compiledLinks[$group->id]);
                    $groupItem['title'] = $group->getTitle();
                    $groupItem['id'] = $group->id;

                    $structureElement->groupsList[] = $groupItem;
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}