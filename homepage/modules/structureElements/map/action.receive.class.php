<?php

class receiveMap extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            $structureElement->structureName = $structureElement->title;
            $structureElement->styles = trim($structureElement->styles);
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            $structureElement->persistElementData();
            $structureElement->persistDisplayMenusLinks();
            $controller->redirect($structureElement->URL);
            $structureElement->setViewName('result');
        } else {
            $structureElement->executeAction("showForm");
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'hideTitle',
            'content',
            'mapCode',
            'country',
            'region',
            'city',
            'address',
            'zip',
            'coordinates',
            'description',
            'displayMenus',
            'image',
            'styles',
            'zoomControlEnabled',
            'zoomLevel',
            'streetViewControlEnabled',
            'mapTypeControlEnabled',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['styles'][] = 'json';
    }
}
