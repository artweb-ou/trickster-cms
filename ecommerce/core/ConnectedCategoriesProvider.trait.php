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
                foreach ($categoryIds as &$categoryId) {
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

    public function getConnectedCategoriesIds()
    {
        if (is_null($this->connectedCategoriesIds)) {
            /**
             * @var linksManager $linksManager
             */
            $linksManager = $this->getService('linksManager');
            $this->connectedCategoriesIds = $linksManager->getConnectedIdList($this->id, $this->structureType . "Category", "parent");
        }
        return $this->connectedCategoriesIds;
    }

    public function updateConnectedCategories($formCategories)
    {
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');

        // check category links
        if ($connectedCategoriesIds = $this->getConnectedCategoriesIds()) {
            foreach ($connectedCategoriesIds as &$connectedCategoryId) {
                if (!in_array($connectedCategoryId, $formCategories)) {
                    $linksManager->unLinkElements($this->id, $connectedCategoryId, $this->structureType . 'Category');
                }
            }
        }
        foreach ($formCategories as $selectedCategoryId) {
            $linksManager->linkElements($this->id, $selectedCategoryId, $this->structureType . 'Category');
        }
        $this->connectedCategoriesIds = null;
    }
}