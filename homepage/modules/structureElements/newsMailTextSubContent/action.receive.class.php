<?php

class receiveNewsMailTextSubContent extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if (!is_null($structureElement->getDataChunk('image')->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk('image')->originalName;
            } elseif ($structureElement->replacementImage) {
                $uploadsPath = $this->getService('PathsManager')->getPath('uploads');
                $oldFile = $uploadsPath . $structureElement->replacementImage;
                $newFile = $uploadsPath . $structureElement->id;
                if (file_exists($oldFile) && is_file($oldFile)) {
                    copy($oldFile, $newFile);
                }
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->id;
            }
            $linksManager = $this->getService('linksManager');
            $categoriesIdIndex = $linksManager->getConnectedIdIndex($structureElement->id, $structureElement::LINK_TYPE_CATEGORY, 'child');
            if ($structureElement->categoryInput) {
                if (!isset($categoriesIdIndex[$structureElement->categoryInput])) {
                    $linksManager->linkElements($structureElement->categoryInput, $structureElement->id,
                        $structureElement::LINK_TYPE_CATEGORY);
                } else {
                    unset($categoriesIdIndex[$structureElement->categoryInput]);
                }
            }
            foreach ($categoriesIdIndex as $categoryId => $value) {
                $linksManager->unLinkElements($categoryId, $structureElement->id, $structureElement::LINK_TYPE_CATEGORY);
            }
            $structureElement->structureName = $structureElement->title;
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->setViewName('form');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'content',
            'image',
            'link',
            'linkName',
            'title',
            'replacementImage',
            'categoryInput',
            'contentStructureType',
            'field1',
            'field2',
            'field3',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}