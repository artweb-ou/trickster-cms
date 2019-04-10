<?php

class deleteProductCatalogue extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');
        if ($elementParent = $structureManager->getElementsFirstParent($structureElement->id)) {
            $categoriesElement = $structureManager->getElementByMarker('categories');
            $categoriesList = $structureManager->getElementsFlatTree($categoriesElement->id);

            $compiledLinks = $linksManager->getElementsLinksIndex($elementParent->id, 'catalogue', 'parent');
            foreach ($categoriesList as &$category) {
                if (isset($compiledLinks[$category->id])) {
                    $compiledLinks[$category->id]->delete();
                }
            }
        }

        $redirectURL = false;
        if (!$structureElement->groupDeletion) {
            $parentElement = $structureManager->getElementsFirstParent($structureElement->id);
            $redirectURL = $parentElement->URL;

            if ($controller->getParameter('view')) {
                $redirectURL .= 'view:' . $controller->getParameter('view') . '/';
            }
        }
        $structureElement->deleteElementData();

        if ($redirectURL) {
            $controller->redirect($redirectURL);
        }
    }
}


