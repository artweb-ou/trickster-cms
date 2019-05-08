<?php

class showProductCatalogue extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param productCatalogueElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($firstParent = $structureManager->getElementsFirstParent($structureElement->id)) {
            $categories = $structureElement->getCategoriesList();
            /**
             * @var categoryElement[] $categories
             */
            foreach ($categories as &$category) {
                $category->setProductCatalogue($structureElement);
                $category->structureRole = $structureElement->structureRole;

                //todo: refactor temporary fix - can this be somehow actively done from category side? This could possibly affect some custom logic on category side.
                if ($category->productsLayout == 'inherit') {
                    $category->productsLayout = $structureElement->productsLayout;
                }
            }
        }
        $structureElement->setViewName('show');
    }
}