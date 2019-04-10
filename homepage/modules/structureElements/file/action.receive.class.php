<?php

class receiveFile extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if ($structureElement->getDataChunk("image")->originalName) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }

            if ($structureElement->alt == '') {
                if ($structureElement->title == '') {
                    $info = pathinfo($structureElement->getDataChunk("image")->originalName);
                    $structureElement->alt = $info['filename'];
                } else {
                    $structureElement->alt = $structureElement->title;
                }
            }

            $structureElement->persistElementData();
        }
        if ($controller->getApplicationName() != 'adminAjax') {
            $firstParent = $structureManager->getElementsFirstParent($structureElement->id);
            if ($firstParent) {
                $controller->redirect($firstParent->URL);
            }
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'file',
            'title',
        ];
    }
}
