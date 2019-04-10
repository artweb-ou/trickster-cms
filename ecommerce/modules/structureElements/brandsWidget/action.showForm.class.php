<?php

class showFormBrandsWidget extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->brandsList = [];

        $brandsElement = $structureManager->getElementByMarker('brands');
        $brandsList = $structureManager->getElementsFlatTree($brandsElement->id);

        $linksManager = $this->getService('linksManager');
        $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'brands', 'parent');

        foreach ($brandsList as &$brand) {
            $brandItem = [];
            if (isset($compiledLinks[$brand->id])) {
                $brandItem['linkExists'] = true;
            } else {
                $brandItem['linkExists'] = false;
            }
            $brandItem['title'] = $brand->title;
            $brandItem['structureName'] = $brand->structureName;
            $brandItem['id'] = $brand->id;

            $structureElement->brandsList[] = $brandItem;
        }

        if ($structureElement->final) {
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}