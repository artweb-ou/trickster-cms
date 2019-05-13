<?php

class ProductIconsManager
{
    const DAILY_SECONDS = 24 * 60 * 60;
    /**
     * @var structureManager
     */
    protected $structureManager;
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
     * @var genericIconElement[]
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
     * @return genericIconElement[]
     */
    public function getProductIcons($product)
    {
        if (!isset($this->icons[$product->id])) {
            $this->icons[$product->id] = [];

            $categories = $product->getConnectedCategories();
            $elementIconsIndex = [];
            foreach ($categories as $category) {
                $elementIconsIndex += $this->linksManager->getConnectedIdIndex($category->id, 'genericIconCategory', 'child');
            //    var_dump($this->linksManager->getConnectedIdIndex($category->id, 'genericIconCategory', 'child'));
            }

            //directly connected global icons
            $elementIconsIndex += $this->linksManager->getConnectedIdIndex($product->id, 'genericIconProduct', 'child');
            $elementIconsIndex += $this->linksManager->getConnectedIdIndex($product->id, 'genericIconParameter', 'child');

        //    var_dump($elementIconsIndex);
            //check all other icons for their logic
            if ($allIcons = $this->iconsManager->getAllIcons()) {
                $now = time();
                $show = [];
                foreach ($allIcons as $iconElement) {
               //     $elementIconsIndex[$iconElement->id] = false;
                    $iconElement;
                    $startDate = $iconElement->getValue('startDate');
                    if ($endDate = $iconElement->getValue('endDate')) {
                        $endDate += self::DAILY_SECONDS;
                    }
                    $dateCreated = $product->getValue('dateCreated');
                    if ($startDate && $endDate) {
                        if ($startDate <= $dateCreated && $endDate >= $dateCreated) {
                        //    $elementIconsIndex[$iconElement->id] = true;
                            $show[] = 'date';
                        }
                    } elseif ($startDate && $startDate <= $dateCreated) {
                     //   $elementIconsIndex[$iconElement->id] = true;
                        $show[] = 'date';
                    } elseif ($endDate && $endDate >= $dateCreated) {
                   //     $elementIconsIndex[$iconElement->id] = true;
                        $show[] = 'date';
                    }

                    if ($dateCreated + $iconElement->days * self::DAILY_SECONDS >= $now) {
                    //    $elementIconsIndex[$iconElement->id] = true;
                        $show[] = 'date';
                    }

                    if (!empty($iconElement->getFormData())) {
                        var_dump($iconElement->getFormData()['iconProductAvail']);
                     //   $iconElement->getFormData();
                    }


                   if (!empty($iconElement->getValue('categories'))) {
                     //   $elementIconsIndex[$iconElement->id] = true;
                     //   $show[] = $iconElement->getValue('categories');
                        var_dump($iconElement->getConnectedCategories());

                    }
/*                     if (!empty($iconElement->getFormData('iconProductAvail'))) {
                     //   $elementIconsIndex[$iconElement->id] = true;
                        $show[] =  $iconElement->getFormData('iconProductAvail');
                    }
                    if (!empty($iconElement->getValue('products'))) {
                    //    $elementIconsIndex[$iconElement->id] = true;
                        $show[] = $iconElement->getValue('products');
                    }
                    if (!empty($iconElement->getValue('brands'))) {
                    //    $elementIconsIndex[$iconElement->id] = true;
                        $show[] = 'brands';
                    }
                    //   $elementIconsIndex[$iconElement->id] = true;
                    if (!empty($iconElement->getValue('parameters'))) {
                    //    $elementIconsIndex[$iconElement->id] = true;
                        $show[] = $iconElement->getValue('parameters');
                    }
                    if (in_array('date', $show) && in_array('categories', $show)){
                    //    var_dump($show);
                    //    $elementIconsIndex[$iconElement->id] = true;
                    }*/

                }

                 //   var_dump($elementIconsIndex);
                foreach ($elementIconsIndex as $iconId => $value) {
                 //   var_dump($iconElement->iconRole);
            //        var_dump($iconElement->getFormData());
                 //   var_dump($iconElement->categories);
                    if($value) {
                        $this->icons[$product->id][] = $this->structureManager->getElementById($iconId);
                    }
                }

            }
       //     var_dump($show);

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