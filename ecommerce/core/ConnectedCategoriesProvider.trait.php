<?php

trait ConnectedCategoriesProviderTrait
{
    /**
     * @var categoryElement[]
     */
    protected $connectedCategories;
    /**
     * @var int[]
     */
    protected $connectedCategoriesIds;

    /**
     * @return array
     */
    public function getConnectedCategoriesInfo()
    {
        $info = [];
        foreach ($this->getConnectedCategories() as $categoryElement) {
            $item = [];
            $item['id'] = $categoryElement->id;
            $item['title'] = $categoryElement->getTitle();
            $item['select'] = true;
            $info[] = $item;
        }

        return $info;
    }

    /**
     * @return categoryElement[]
     */
    public function getConnectedCategories()
    {
        if ($this->connectedCategories === null) {
            $this->connectedCategories = [];
            if ($categoryIds = $this->getConnectedCategoriesIds()) {
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                foreach ($categoryIds as $categoryId) {
                    if ($categoryId && $categoryElement = $structureManager->getElementById($categoryId)) {
                        $this->connectedCategories[] = $categoryElement;
                    }
                }
            }
        }
        return $this->connectedCategories;
    }

    /**
     * @param null $linkType
     * @return int[]
     */
    public function getConnectedCategoriesIds($linkType = null)
    {
        if (!$linkType) {
            $linkType = $this->structureType . 'Category';
        }
        if ($this->connectedCategoriesIds === null) {
            /**
             * @var linksManager $linksManager
             */
            $linksManager = $this->getService('linksManager');
            $this->connectedCategoriesIds = $linksManager->getConnectedIdList($this->id, $linkType, "parent");
        }
        return $this->connectedCategoriesIds;
    }

    public function updateConnectedCategories($formCategories, $linkType = null)
    {
        if (!$linkType) {
            $linkType = $this->structureType . 'Category';
        }
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');

        // check category links
        if ($connectedCategoriesIds = $this->getConnectedCategoriesIds()) {
            foreach ($connectedCategoriesIds as $connectedCategoryId) {
                if (!in_array($connectedCategoryId, $formCategories)) {
                    $linksManager->unLinkElements($this->id, $connectedCategoryId, $linkType);
                }
            }
        }
        foreach ($formCategories as $selectedCategoryId) {
            $linksManager->linkElements($this->id, $selectedCategoryId, $linkType);
        }
        $this->connectedCategoriesIds = null;
        $this->connectedCategories = null;
    }
}