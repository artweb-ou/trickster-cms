<?php

class bannerElement extends structureElement implements ImageUrlProviderInterface
{
    use ImageUrlProviderTrait;

    public $dataResourceName = 'module_banner';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $connectedCategories;
    protected $connectedCategoriesIds;
    protected $categories;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['views'] = 'text';
        $moduleStructure['clicks'] = 'text';
        $moduleStructure['width'] = 'text';
        $moduleStructure['height'] = 'text';
        $moduleStructure['type'] = 'text';
        $moduleStructure['link'] = 'url';
        $moduleStructure['clickTag'] = 'text';
        $moduleStructure['openInNewWindow'] = 'checkbox';
        $moduleStructure['bannerCategoryIds'] = 'numbersArray';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function getConnectedCategories()
    {
        if (is_null($this->connectedCategories)) {
            $this->connectedCategories = [];
            if ($categories = $this->getCategories()) {
                $connectedCategoriesIds = $this->getConnectedCategoriesIds();
                foreach ($categories as &$categoryElement) {
                    $category['id'] = $categoryElement->id;
                    $category['title'] = $categoryElement->getTitle();
                    $category['select'] = false;
                    if (in_array($categoryElement->id, $connectedCategoriesIds)) {
                        $category['select'] = true;
                    }
                    $this->connectedCategories[] = $category;
                }
            }
        }
        return $this->connectedCategories;
    }

    /**
     * @return int[]
     */
    public function getConnectedCategoriesIds()
    {
        if (is_null($this->connectedCategoriesIds)) {
            $linksManager = $this->getService('linksManager');
            $this->connectedCategoriesIds = $linksManager->getConnectedIdList($this->id, "bannerCategoryBanner", "child");
        }
        return $this->connectedCategoriesIds;
    }

    /**
     * @return bannerCategoryElement[]
     */
    public function getCategories()
    {
        if (is_null($this->categories)) {
            $structureManager = $this->getService('structureManager');
            $this->categories = $structureManager->getElementsByType('bannerCategory');
        }
        return $this->categories;
    }

    public function recordView()
    {
        $collection = persistableCollection::getInstance($this->dataResourceName);
        $collection->updateData(['views' => $this->views + 1], [
            'id' => $this->id,
        ]);
    }
}