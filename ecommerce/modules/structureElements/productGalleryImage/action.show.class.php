<?php

class showProductGalleryImage extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('pollQuestion.show.tpl');
    }
}

