<?php

class shopCategoryElement extends structureElement
{
    use MetadataProviderTrait;
    public $dataResourceName = 'module_shopcategory';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $shopsList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['color'] = 'text';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['metaDescriptionTemplate'] = 'text';
        $moduleStructure['metaTitleTemplate'] = 'text';
        $moduleStructure['metaH1Template'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields = [
            'title',
            'metaTitle',
            'metaDescription',
            'metaDescriptionTemplate',
            'metaTitleTemplate',
            'metaH1Template',
        ];
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getShopsList()
    {
        if ($this->shopsList === null) {
            $structureManager = $this->getService('structureManager');
            $this->shopsList = $structureManager->getElementsChildren($this->id, null,
                'shopCategory');
            $titles = [];
            foreach ($this->shopsList as &$element) {
                $titles[] = mb_strtolower($element->title);
            }
            array_multisort($titles, SORT_ASC, $this->shopsList);
        }
        return $this->shopsList;
    }
}


