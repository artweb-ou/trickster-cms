<?php

class receiveShopCatalogue extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');
            $structureElement->prepareActualData();

            if ($structureElement->title == '') {
                $structureElement->structureName = $structureElement->structureType . $structureElement->id;
            } else {
                $structureElement->structureName = $structureElement->title;
            }
            $structureElement->persistElementData();

            $categoriesElement = $structureManager->getElementByMarker('shopCategories');
            $categoriesList = $structureManager->getElementsFlatTree($categoriesElement->id);

            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id,
                $structureElement::LINK_TYPE_CATEGORY, 'parent');

            if (in_array('all', $structureElement->categories)) {
                foreach ($categoriesList as &$category) {
                    if (isset($compiledLinks[$category->id])) {
                        $compiledLinks[$category->id]->delete();
                    }
                }
            } else {
                foreach ($categoriesList as &$category) {
                    if (isset($compiledLinks[$category->id]) && !in_array($category->id, $structureElement->categories)) {
                        $compiledLinks[$category->id]->delete();
                    } elseif (!isset($compiledLinks[$category->id]) && in_array($category->id, $structureElement->categories)) {
                        $linksManager->linkElements($structureElement->id, $category->id,
                            $structureElement::LINK_TYPE_CATEGORY);
                    }
                }
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->setViewName('form');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'categories',
            'columns',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


