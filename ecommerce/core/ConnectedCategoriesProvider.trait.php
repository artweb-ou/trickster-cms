<?php

trait ConnectedCategoriesProviderTrait
{
    protected $connectedCategories;
    protected $connectedCategoriesIds;

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
                        $item = [];
                        $item['id'] = $categoryElement->id;
                        $item['title'] = $categoryElement->getTitle();
                        $item['select'] = true;
                        $this->connectedCategories[] = $item;
                    }
                }
            }
        }
        return $this->connectedCategories;
    }

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
    }
}