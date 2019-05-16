<?php

class receiveFile extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param fileElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if ($structureElement->getDataChunk("file")->originalName) {
                $structureElement->file = $structureElement->id;
                $structureElement->fileName = $structureElement->getDataChunk("file")->originalName;
            }

            if ($structureElement->getDataChunk("image")->originalName) {
                $structureElement->image = $structureElement->id . 'image';
                $structureElement->imageFileName = $structureElement->getDataChunk("image")->originalName;
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
            $controller->redirect($structureElement->getUrl('showForm'));
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'file',
            'image',
            'title',
        ];
    }
}
