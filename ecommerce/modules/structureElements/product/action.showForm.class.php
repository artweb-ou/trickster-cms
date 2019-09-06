<?php

class showFormProduct extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param productElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');

        if ($structureElement->final) {
            $externalCategoryId = false;
            if ($controller->getParameter('categoryId')) {
                $externalCategoryId = intval($controller->getParameter('categoryId'));
            }

            //PREPARE CATEGORIES SELECTOR
            $compiledLinks = [];
            if ($elementLinks = $linksManager->getElementsLinks($structureElement->id, 'catalogue', 'child')) {
                foreach ($elementLinks as &$link) {
                    $categoryId = $link->parentStructureId;
                    $compiledLinks[$categoryId] = $link;
                }
            }

            $structureElement->categoriesList = [];
            if ($categoriesFolder = $structureManager->getElementByMarker('categories')) {
                $categoriesList = $structureManager->getElementsFlatTree($categoriesFolder->id, 'container');

                foreach ($categoriesList as &$category) {
                    $categoryItem = [];
                    $categoryItem['level'] = $category->level - 3;
                    if (isset($compiledLinks[$category->id]) || $externalCategoryId == $category->id) {
                        $categoryItem['select'] = true;
                    } else {
                        $categoryItem['select'] = false;
                    }
                    $categoryItem['title'] = $category->getTitle();
                    $categoryItem['id'] = $category->id;

                    $structureElement->categoriesList[] = $categoryItem;
                }
            }

            //LOAD VARIATIONS
            $structureElement->variations = [];
            if ($childElements = $structureElement->getChildrenList()) {
                foreach ($childElements as &$childElement) {
                    if ($childElement->structureType == 'productVariation') {
                        $structureElement->variations[] = $childElement;
                    }
                }
            }

            //CONNECTED PRODUCTS
            $compiledLinks = [];
            if ($connectedProductLinks = $linksManager->getElementsLinks($structureElement->id, 'connected')) {
                //getbyidlist
                foreach ($connectedProductLinks as &$link) {
                    $productId = $link->childStructureId;
                    $compiledLinks[$productId] = $link;
                }
            }

            // this is for the new ajax select solution
            $structureElement->productsInfo = [];
            if ($connectedProductIds = $linksManager->getConnectedIdList($structureElement->id, 'connected')) {
                foreach ($connectedProductIds as &$connection) {
                    $connectedProducts[] = $structureManager->getElementById($connection);
                    $product = $structureManager->getElementById($connection);
                    if (!empty($product)) {
                        $productInfo['select'] = true;
                        $productInfo['title'] = $product->getTitle();
                        $productInfo['id'] = $product->id;
                    }
                    $structureElement->productsInfo[] = $productInfo;
                }
            }

            // this is for the new ajax select solution
            $structureElement->productsInfo2 = [];
            if ($connectedProductIds = $linksManager->getConnectedIdList($structureElement->id, 'connected2')) {
                foreach ($connectedProductIds as &$connection) {
                    $connectedProducts[] = $structureManager->getElementById($connection);
                    $product = $structureManager->getElementById($connection);

                    $productInfo['select'] = true;
                    $productInfo['title'] = $product->getTitle();
                    $productInfo['id'] = $product->id;

                    $structureElement->productsInfo2[] = $productInfo;
                }
            }

            // this is for the new ajax select solution
            $structureElement->productsInfo3 = [];
            if ($connectedProductIds = $linksManager->getConnectedIdList($structureElement->id, 'connected3')) {
                foreach ($connectedProductIds as &$connection) {
                    $connectedProducts[] = $structureManager->getElementById($connection);
                    $product = $structureManager->getElementById($connection);

                    $productInfo['select'] = true;
                    $productInfo['title'] = $product->getTitle();
                    $productInfo['id'] = $product->id;

                    $structureElement->productsInfo3[] = $productInfo;
                }
            }

            // CONNECTED CATEGORY
            $compiledLinks = [];
            if ($connectedCategoriesLinks = $linksManager->getElementsLinks($structureElement->id, 'connectedCategory')) {
                //getbyidlist
                foreach ($connectedCategoriesLinks as &$link) {
                    $categoryId = $link->childStructureId;
                    $compiledLinks[$categoryId] = $link;
                }
            }

            $structureElement->connectedProductCategoriesInfo = [];
            if ($connectedCategoriesIds = $linksManager->getConnectedIdList($structureElement->id, 'connectedCategory')) {
                foreach ($connectedCategoriesIds as &$connection) {
                    $connectedCategories[] = $structureManager->getElementById($connection);
                    $category = $structureManager->getElementById($connection);

                    $categoryInfo['title'] = $category->getTitle();
                    $categoryInfo['id'] = $category->id;
                    $categoryInfo['select'] = true;

                    $structureElement->connectedProductCategoriesInfo[] = $categoryInfo;
                }
            }

            // PRODUCT BRAND
            if ($idList = $linksManager->getConnectedIdList($structureElement->id, 'productbrand', 'child')) {
                $structureElement->brandId = reset($idList);
            }
            if ($brandsFolder = $structureManager->getElementByMarker('brands')) {
                $structureElement->brandsList = $structureManager->getElementsChildren($brandsFolder->id);
                $brandsList = [];
                $formData = $structureElement->getFormData();
                $selectedBrandId = $formData['brandId'];
                foreach ($structureElement->brandsList as &$element) {
                    $item = [];
                    $item['id'] = $element->id;
                    $item['title'] = $element->getTitle();
                    $item['select'] = $element->id == $selectedBrandId;
                    $brandsList[] = $item;
                }
                $structureElement->brandsList = $brandsList;
            }

            // Discounts
            if ($discountsFolder = $structureManager->getElementByMarker('discounts')) {
                $structureElement->discountsList = $structureManager->getElementsChildren($discountsFolder->id);
                $sortParameter = [];
                $discounts = [];
                $selectedDiscountsId = $structureElement->getAllConnectedDiscounts();
                foreach ($structureElement->discountsList as &$element) {
                    $item = [];
                    $item['id'] = $element->id;
                    $item['title'] = $element->getTitle();
                    $item['select'] = in_array($element, $structureElement->getAllConnectedDiscounts());
                    $discounts[] = $item;
                }
                $structureElement->discountsList = $discounts;
            }

            $structureElement->setTemplate('shared.content.tpl');
            $structureElement->allParametersGroups = $structureElement->getParametersInfoList();
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
            $renderer->assign('connectedDiscounts', $structureElement->getAllConnectedDiscounts());
        }
    }
}