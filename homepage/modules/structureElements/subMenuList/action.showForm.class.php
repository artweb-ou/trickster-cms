<?php

class showFormSubMenuList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $linksManager = $this->getService('linksManager');
            $linksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'submenulist', 'parent');

            if ($languageElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                $menusList = $structureManager->getElementsFlatTree($languageElement->id, 'container', $structureElement->getMenuLinkTypes(), false);

                foreach ($menusList as $element) {
                    if ($element->structureRole == 'container' || $element->structureRole == 'hybrid') {
                        $item = [];
                        $item['id'] = $element->id;
                        $item['level'] = $element->level - 3;
                        $item['title'] = $element->getTitle();
                        $item['select'] = isset($linksIndex[$element->id]);
                        $structureElement->menusList[] = $item;
                    }
                }
                if ($structureElement->final) {
                    $structureElement->levelsList = range(1, 6);
                    $structureElement->maxLevelsList = range(1, 6);
                    $structureElement->setTemplate('shared.content.tpl');
                    $renderer = $this->getService('renderer');
                    $renderer->assign('contentSubTemplate', 'component.form.tpl');
                    $renderer->assign('form', $structureElement->getForm('form'));
                }
            }
        }
    }
}