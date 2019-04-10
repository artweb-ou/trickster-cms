<?php

class showFormProductCatalogue extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setReplacementElements([$structureElement]);
        $structureElement->categoriesList = [];

        if ($structureElement->final) {
            if ($categoriesElement = $structureManager->getElementByMarker('categories')) {
                $categoriesList = $structureManager->getElementsFlatTree($categoriesElement->id);

                $compiledLinks = [];
                if ($elementParent = $structureManager->getElementsFirstParent($structureElement->id)) {
                    $linksManager = $this->getService('linksManager');
                    $compiledLinks = $linksManager->getElementsLinksIndex($elementParent->id, 'catalogue', 'parent');
                }

                foreach ($categoriesList as &$category) {
                    $categoryItem = [];
                    $categoryItem['level'] = $category->level - 3;

                    $categoryItem['title'] = $category->getTitle();
                    $categoryItem['id'] = $category->id;
                    $categoryItem['select'] = isset($compiledLinks[$category->id]);
                    $structureElement->categoriesList[] = $categoryItem;
                }
            }

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}