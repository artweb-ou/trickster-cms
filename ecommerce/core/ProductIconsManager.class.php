<?php

class ProductIconsManager
{
    const DAILY_SECONDS = 24 * 60 * 60;
    /**
     * @var structureManager
     */
    protected $structureManager;
    /**
     * @var parametersManager
     */
    protected $parametersManager;
    /**
     * @var Illuminate\Database\Capsule\Manager()
     */
    protected $db;
    /**
     * @var IconsManager $iconsManager
     */
    protected $iconsManager;
    /**
     * @var linksManager
     */
    protected $linksManager;
    /**
     * @var genericIconElement[][]
     */
    protected $icons = [];
    /**
     * @var galleryImageElement[]
     */
    protected $ownIcons = [];
    protected $iconProducts = [];

    /**
     * @param mixed $structureManager
     */
    public function setStructureManager($structureManager)
    {
        $this->structureManager = $structureManager;
    }

    /**
     * @param $parametersManager
     */
    public function setParametersManager($parametersManager)
    {
        $this->parametersManager = $parametersManager;
    }

    /**
     * @param Illuminate\Database\Capsule\Manager() $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * @param mixed $linksManager
     */
    public function setLinksManager($linksManager)
    {
        $this->linksManager = $linksManager;
    }

    /**
     * @param mixed $iconsManager
     */

    public function setIconsManager($iconsManager)
    {
        $this->iconsManager = $iconsManager;
    }

    /**
     * @param productElement $product
     * @return genericIconElement[]|galleryImageElement[]
     */
    public function getProductIcons($product)
    {
        if (!isset($this->icons[$product->id])) {
            $this->icons[$product->id] = [];
            $elementIconsIndex = [];

            /**
             * @var categoryElement[] $categories
             */
            $categories = $product->getDeepParentCategories();

            if ($allIcons = $this->iconsManager->getAllIcons()) {
                $now = time();

                foreach ($allIcons as $iconElement) {
                    if (!$iconElement->applicableToAllProducts) {
                        $productFilterExisting = false;

                        //if icon has products filter, check if product ID matches
                        if ($iconConnectedProductsIds = $iconElement->getConnectedProductsIds()) {
                            $productFilterExisting = true;
                            if (!in_array($product->id, $iconConnectedProductsIds)) {
                                continue;
                            }
                        }

                        //if icon has categories filter, check it first
                        if ($iconConnectedCategoriesIds = $iconElement->getConnectedCategoriesIds()) {
                            $productFilterExisting = true;
                            $productCategoriesIds = array_column($categories, 'id');
                            if (!array_intersect($productCategoriesIds, $iconConnectedCategoriesIds)) {
                                continue;
                            }
                        }

                        //if icon has brands filter, check if product brand ID matches
                        if ($iconConnectedBrandsIds = $iconElement->getConnectedBrandsIds()) {
                            $productFilterExisting = true;
                            if (!in_array($product->brandId, $iconConnectedBrandsIds)) {
                                continue;
                            }
                        }

                        //if icon doesn't have checkbox "applicableToAllProducts" and no filters are set, then no products are shown for this icon
                        if (!$productFilterExisting) {
                            continue;
                        }
                    }

                    $iconApplicable = false;
                    switch ($iconElement->getProductIconRoleType($iconElement->iconRole)) {
                        case 'role_date':
                            $startDate = $iconElement->getValue('startDate');
                            if ($endDate = $iconElement->getValue('endDate')) {
                                $endDate += self::DAILY_SECONDS;
                            }
                            $dateCreated = $product->getValue('dateCreated');
                            if ($startDate && $endDate) {
                                if ($startDate <= $dateCreated && $endDate >= $dateCreated) {
                                    $iconApplicable = true;
                                }
                            } elseif ($startDate && $startDate <= $dateCreated) {
                                $iconApplicable = true;
                            } elseif ($endDate && $endDate >= $dateCreated) {
                                $iconApplicable = true;
                            }

                            if ($dateCreated + $iconElement->days * self::DAILY_SECONDS >= $now) {
                                $iconApplicable = true;
                            }
                            break;
                        case 'role_availability':
                            if (!empty($iconProductAvail = $iconElement->iconProductAvail)) {
                                if (in_array($product->availability, $iconProductAvail)) {
                                    $iconApplicable = true;
                                }
                            }
                            break;
                        case 'role_general_discount':
                            if ($product->getDiscountAmount(false) > 0) {
                                $iconApplicable = true;
                            }
                            break;
                        case 'role_by_parameter':
                            if (($iconConnectedParametersIds = $iconElement->getConnectedParametersIds()) && ($parametersInfoList = $product->getParametersInfoList())) {
                                $productSelectionOptions = [];
                                foreach ($parametersInfoList as $parameterInfoKey => $parameterInfoValue) {
                                    if ($parameterInfoValue['structureType'] == 'productSelection') {
                                        $productSelectionOptions = array_merge($productSelectionOptions, $parameterInfoValue['productOptions']);
                                        /*
                                           'title' =>string
                                           'id' =>int
                                           'originalName' =>string
                                           'image' =>string
                                           'value' =>string
                                        */
                                    }
                                }
                                // get ID list of this parameters List
                                $productSelectionOptionsIds = array_column($productSelectionOptions, 'id');
                                if (array_intersect($productSelectionOptionsIds, $iconConnectedParametersIds)) {
                                    $iconApplicable = true;
                                }
                            }
                            break;
                        default:
                            $iconApplicable = true;
                            break;
                    }
                    if ($iconApplicable) {
                        $this->icons[$product->id][] = $iconElement;
                    }
                }
            }

            //add product's own icons
            if ($ownIcons = $this->getOwnIcons($product->id, $product->structureType)) {
                $this->icons[$product->id] = array_merge($this->icons[$product->id], $ownIcons);
            }

            //add parent categories' own and global unique icons
            foreach ($categories as $category) {
                if ($categoryIcons = $this->getCategoryIcons($category)) {
                    foreach ($categoryIcons as $categoryIcon) {
                        if (!isset($elementIconsIndex[$categoryIcon->id])) {
                            $this->icons[$product->id][] = $categoryIcon;
                            $elementIconsIndex[$categoryIcon->id] = true;
                        }
                    }
                }
            }
        }
        return $this->icons[$product->id];
    }

    /**
     * @param categoryElement $category
     * @return mixed
     */
    public function getCategoryIcons($category)
    {
        if (!isset($this->icons[$category->id])) {
            //category's own icons
            $this->icons[$category->id] = $this->getOwnIcons($category->id, $category->structureType);

            //parent categories' own icons
            $parentCategory = $category;
            while ($parentCategory = $parentCategory->getParentCategory()) {
                if ($parentCategoryIcons = $this->getOwnIcons($parentCategory->id, $parentCategory->structureType)) {
                    $this->icons[$category->id] = array_merge($this->icons[$category->id], $parentCategoryIcons);
                }
            }
        }
        return $this->icons[$category->id];
    }

    public function getOwnIcons($id, $elementType)
    {
        if (!isset($this->ownIcons[$id])) {
            $this->ownIcons[$id] = $this->structureManager->getElementsChildren($id, null, $elementType . 'Icon');
        }
        return $this->ownIcons[$id];
    }

    public function getIconProductIds($iconId)
    {
        if (!isset($this->iconProducts[$iconId])) {
            $this->iconProducts[$iconId] = [];
        }
        if ($iconElement = $this->getIconElement($iconId)) {
            $this->iconProducts[$iconId] = $iconElement->getConnectedProductsIds();
            if ($ownCategoryIds = $iconElement->getConnectedCategoriesIds()) {
                foreach ($ownCategoryIds as $categoryId) {
                    /**
                     * @var categoryElement $categoryElement
                     */
                    if ($categoryElement = $this->structureManager->getElementById($categoryId)) {
                        if ($categoryProductIds = $categoryElement->getConnectedProductsIds()) {
                            $this->iconProducts[$iconId] = array_merge($this->iconProducts[$iconId], $categoryProductIds);
                        }
                    }
                }
            }
            if ($iconElement->days) {
                if ($records = $this->db->table('structure_elements')
                    ->select('id')->distinct()
                    ->where('structureType', '=', 'product')
                    ->where('dateCreated', '>=', time() - self::DAILY_SECONDS * $iconElement->days)
                    ->get()
                ) {
                    $this->iconProducts[$iconId] = array_merge($this->iconProducts[$iconId], array_column($records, 'id'));
                };
            }
            if ($iconElement->startDate || $iconElement->endDate) {
                $query = $this->db->table('structure_elements')
                    ->select('id')
                    ->distinct()
                    ->where('structureType', '=', 'product');
                if ($iconElement->startDate) {
                    $query->where('dateCreated', '>=', $iconElement->getValue('startDate'));
                }
                if ($iconElement->endDate) {
                    $query->where('dateCreated', '<=', $iconElement->getValue('endDate') + self::DAILY_SECONDS);
                }
                if ($records = $query->get()) {
                    $this->iconProducts[$iconId] = array_merge($this->iconProducts[$iconId], array_column($records, 'id'));
                };
            }
        }
        return $this->iconProducts[$iconId];
    }

    public function getIconElement($iconId)
    {
        if ($allIcons = $this->iconsManager->getAllIcons()) {
            foreach ($allIcons as $iconElement) {
                if ($iconElement->id == $iconId) {
                    return $iconElement;
                }
            }
        }
        return false;
    }
}