<?php

class showFormBrand extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->brandsListsList = [];
            $connectedBrandsListsIds = $structureElement->getConnectedBrandsListsIds();
            if ($brandsListElements = $structureManager->getElementsByType('brandsList')) {
                foreach ($brandsListElements as &$brandsListElement) {
                    if ($brandsListElement->connectAll) {
                        continue;
                    }
                    $item = [];
                    $item['id'] = $brandsListElement->id;
                    $item['title'] = $brandsListElement->getTitle();
                    $item['select'] = in_array($brandsListElement->id, $connectedBrandsListsIds);
                    $structureElement->brandsListsList[] = $item;
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
        }
    }
}