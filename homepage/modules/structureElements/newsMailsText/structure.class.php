<?php

class newsMailsTextElement extends structureElement
{
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_newsmailstext';
    protected $allowedTypes = [
        'newsMailTextSubContent',
    ];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $subContentElementsCategoryIndex;

    protected function getTabsList()
    {
        return [
            'showForm',
            'showStatistics',
            'showStatuses',
            'showPositions',
            'showPrivileges',
        ];
    }
    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['from'] = 'text';
        $moduleStructure['fromEmail'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['file'] = 'file';
        $moduleStructure['originalName'] = 'text';
        $moduleStructure['selectedEmails'] = 'array';
        $moduleStructure['selectedGroupsIds'] = 'array';
        $moduleStructure['customTemplate'] = 'text';
    }

    public function getSubContentElements()
    {
        if ($childrenList = $this->getChildrenList()) {
            foreach ($childrenList as $key => $element) {
                if ($element->structureType != 'newsMailTextSubContent') {
                    unset($childrenList[$key]);
                }
            }
        }
        return $childrenList;
    }

    public function getSubCategories()
    {
        if ($childrenList = $this->getChildrenList()) {
            foreach ($childrenList as $key => $element) {
                if ($element->structureType != 'newsMailSubContentCategory') {
                    unset($childrenList[$key]);
                }
            }
        }
        return $childrenList;
    }

    public function getCategoryElements()
    {
        $categories = [];
        $structureManager = $this->getService('structureManager');
        $subContentCategoriesElement = $structureManager->getElementByMarker('newsMailSubContentCategories');
        if ($subContentCategoriesElement) {
            $globalCategories = $subContentCategoriesElement->getChildrenList();
            foreach ($globalCategories as $globalCategory) {
                if ($globalCategory->code) {
                    $categories[$globalCategory->code] = $globalCategory;
                } else {
                    $categories[] = $globalCategory;
                }
            }
        }
        if ($localCategories = $this->getSubCategories()) {
            foreach ($localCategories as $localCategory) {
                if ($localCategory->code) {
                    $categories[$localCategory->code] = $localCategory;
                } else {
                    $categories[] = $localCategory;
                }
            }
        }

        return $categories;
    }

    protected function getIndexedSubContentElements()
    {
        if ($this->subContentElementsCategoryIndex === null) {
            $this->subContentElementsCategoryIndex = [];
            foreach ($this->getSubContentElements() as $subContentElement) {
                if (!$categoryCode = $subContentElement->getCategoryCode()) {
                    $categoryCode = 'no';
                }
                if (!isset($this->subContentElementsCategoryIndex[$categoryCode])) {
                    $this->subContentElementsCategoryIndex[$categoryCode] = [];
                }
                $this->subContentElementsCategoryIndex[$categoryCode][] = $subContentElement;
            }
        }
        return $this->subContentElementsCategoryIndex;
    }

    public function getSubContentElementsByCategory($categoryCode)
    {
        $index = $this->getIndexedSubContentElements();
        return isset($index[$categoryCode]) ? $index[$categoryCode] : [];
    }

    public function getDispatchmentData()
    {
        $result = [
            'title' => $this->title,
            'content' => $this->content,
            'subContents' => [],
            'categories' => [],
            'socialLinks' => $this->getSocialMediaUrls(),
        ];
        $subContentsData = &$result['subContents'];
        $subContentsIndex = $this->getIndexedSubContentElements();
        foreach ($subContentsIndex as $categoryCode => $subContentElements) {
            $subContentsData[$categoryCode] = [];
            foreach ($subContentElements as &$subContentElement) {
                $itemData = [];
                $itemData['title'] = $subContentElement->title;
                $itemData['image'] = $subContentElement->image;
                $itemData['content'] = html_entity_decode($subContentElement->content, ENT_QUOTES);
                $itemData['link'] = $subContentElement->link;
                $itemData['linkName'] = $subContentElement->linkName;
                $itemData['type'] = $subContentElement->type;
                $itemData['contentStructureType'] = $subContentElement->contentStructureType;
                $itemData['field1'] = $subContentElement->field1;
                $itemData['field2'] = $subContentElement->field2;
                $itemData['field3'] = $subContentElement->field3;
                $subContentsData[$categoryCode][] = $itemData;
            }
        }
        if ($categories = $this->getCategoryElements()) {
            foreach ($categories as $categoryCode => $category) {
                $itemData = [];
                $itemData['title'] = $category->title;
                $itemData['code'] = $categoryCode;
                $result['categories'][$categoryCode] = $itemData;
            }
        }
        return $result;
    }

    public function getDispatchmentType()
    {
        if ($this->customTemplate) {
            return 'newsMail' . ucfirst($this->customTemplate);
        }
        return 'newsMail';
    }

    public function getSocialMediaUrls()
    {
        return socialSettingsManager::getInstance()->getSocialMediaUrls();
    }

    public function getTemplateOptions()
    {
        if ($templates = $this->getService('ConfigManager')->get('newsmails.customTemplates')) {
            return $templates;
        }
        return [];
    }
}