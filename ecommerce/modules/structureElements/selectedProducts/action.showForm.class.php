<?php

class showFormSelectedProducts extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');
        $renderer = $this->getService('renderer');

        if ($structureElement->requested) {
            $structureElement->productsInfo = [];
            if ($connectedProductIds = $linksManager->getConnectedIdList($structureElement->id, 'selectedProducts', 'parent')
            ) {
                foreach ($connectedProductIds as $id) {
                    if ($product = $structureManager->getElementById($id)) {
                        $productInfo = [];
                        $productInfo['title'] = $product->getTitle();
                        $productInfo['id'] = $product->id;
                        $productInfo['select'] = true;

                        $structureElement->productsInfo[] = $productInfo;
                    }
                }
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}