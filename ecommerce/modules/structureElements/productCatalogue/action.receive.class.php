<?php

class receiveProductCatalogue extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            // save data
            $structureElement->prepareActualData();

            if ($structureElement->title != '') {
                $structureElement->structureName = $structureElement->title;
            }
            if (in_array('all', $structureElement->categories)) {
                $structureElement->connectAllCategories = 1;
            } else {
                $structureElement->connectAllCategories = 0;
            }
            $structureElement->persistElementData();

            // save relations
            $linksManager = $this->getService('linksManager');

            // categories & products
            if ($elementParent = $structureElement->getContainerElement()) {
                $categoriesLinksIndex = $linksManager->getElementsLinksIndex($elementParent->id, 'catalogue', 'parent');
                $productsLinksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'productCatalogueProduct', 'parent');

                if ($structureElement->categorized) {
                    if (in_array('all', $structureElement->categories)) {
                        //link all top-level categories
                        $categoriesElement = $structureManager->getElementByMarker('categories');
                        $categoriesList = $structureManager->getElementsChildren($categoriesElement->id);
                        foreach ($categoriesList as &$category) {
                            $linksManager->linkElements($elementParent->id, $category->id, 'catalogue');
                            if (isset($categoriesLinksIndex[$category->id])) {
                                unset($categoriesLinksIndex[$category->id]);
                            }
                        }
                    } else {
                        //link manually selected categories
                        foreach ($structureElement->categories as &$categoryId) {
                            if (!isset($categoriesLinksIndex[$categoryId])) {
                                $linksManager->linkElements($elementParent->id, $categoryId, 'catalogue');
                            } else {
                                unset($categoriesLinksIndex[$categoryId]);
                            }
                        }
                    }
                } else {
                    //link all products
                    $productElements = $structureManager->getElementsByType('product');
                    foreach ((array)$productElements as $productElement) {
                        if (!isset($productsLinksIndex[$productElement->id])) {
                            $linksManager->linkElements($structureElement->id, $productElement->id, 'productCatalogueProduct');
                        } else {
                            unset($productsLinksIndex[$productElement->id]);
                        }
                    }
                }
                //remove connection links with deleted/unselected categories
                foreach ($categoriesLinksIndex as &$categoryLink) {
                    $categoryLink->delete();
                }
                //remove connection links with deleted/unselected products
                foreach ($productsLinksIndex as &$productLink) {
                    $productLink->delete();
                }
            }
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'categories',
            'columns',
            'categorized',
            'structureRole',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


