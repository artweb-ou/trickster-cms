<?php

class cloneProduct extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $parentElement = $structureManager->getElementsFirstParent($structureElement->id);
        $copyData = $structureManager->copyElements((array)$structureElement->id, $parentElement->id, [
            'structure',
            'headerContent',
            'leftColumn',
            'rightColumn',
            'bottomMenu',
            'freeBlocks',
            'subArticle',
        ]);

        if ($structureElement->final && $copyData) {
            $clone = $structureManager->getElementById($copyData[$structureElement->id]);
            $controller->redirect($clone->URL);
        }
    }
}


