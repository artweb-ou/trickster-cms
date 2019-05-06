<?php

/**
 * Class shopCatalogueElement
 *
 * @property string $columns
 */
class shopCatalogueElement extends structureElement implements ColumnsTypeProvider
{
    public $dataResourceName = 'module_shop_catalogue';
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $replacementElements = [];
    public $requestedCategory;
    protected $searchCutLength = 200;
    protected $shopsAlfaIndex;
    const LINK_TYPE_CATEGORY = 'shopCatalogueCategory';
    protected $filteredIndex;
    protected $filteredShops;
    protected $filteredCategories;
    protected $inhabitedCategories;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['categories'] = 'numbersArray';
        $moduleStructure['columns'] = 'text';
        $moduleStructure['phrase'] = 'text';
    }

    public function performSearch($phrase)
    {
        $languagesManager = languagesManager::getInstance();
        $currentLanguage = $languagesManager->getCurrentLanguageId();

        $foundElements = [];

        $collection = persistableCollection::getInstance('module_shop');
        $conditions = [];
        $conditions[] = ['column' => 'languageId', 'action' => 'equals', 'argument' => $currentLanguage];
        $conditions[] = [
            'column' => ['title', 'content', 'introduction'],
            'action' => 'like',
            'argument' => '%%' . $phrase . '%%',
        ];
        if ($rows = $collection->conditionalLoad('id', $conditions)) {
            foreach ($rows as &$row) {
                $foundElements[] = $this->prepareSearchResult($row['id'], [
                    'title',
                    'content',
                    'introduction',
                ], $phrase);
            }
        }

        return $foundElements;
    }

    protected function prepareSearchResult($id, $fields, $phrase)
    {
        $structureManager = $this->getService('structureManager');

        if ($element = $structureManager->getElementById($id)) {
            $element->searchContent = false;
            $element->searchTitle = $element->title;
            if (!is_array($fields)) {
                $fields = [$fields];
            }
            foreach ($fields as &$field) {
                if ($value = $element->$field) {
                    $textValue = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
                    if (($position = mb_stripos($textValue, $phrase)) !== false) {
                        $phraseLength = mb_strlen($phrase);

                        if ($field == 'title') {
                            $searchTitle = htmlspecialchars($textValue, ENT_QUOTES, 'UTF-8');
                            $searchTitle = preg_replace('/' . $phrase . '/', '<span class="search_emphasized">' . $phrase . '</span>', $searchTitle);
                            $element->searchTitle = $searchTitle;
                        } else {
                            $searchContent = '';

                            $textLength = mb_strlen($textValue);
                            if ($position > $this->searchCutLength) {
                                $start = $position - $this->searchCutLength;
                            } else {
                                $start = 0;
                            }
                            if ($position + $phraseLength + $this->searchCutLength < $textLength) {
                                $end = $position + $phraseLength + $this->searchCutLength;
                            } else {
                                $end = $textLength;
                            }
                            $searchContentLength = $end - $start;
                            $searchContent = mb_substr($textValue, $start, $searchContentLength);
                            if ($start > 0) {
                                $searchContent = '...' . $searchContent;
                            }
                            if ($end < $textLength) {
                                $searchContent = $searchContent . '...';
                            }

                            $searchContent = htmlspecialchars($searchContent, ENT_QUOTES, 'UTF-8');
                            $searchContent = preg_replace('/' . $phrase . '/i', '<span class="search_emphasized">' . $phrase . '</span>', $searchContent);
                            $element->introduction = $searchContent;
                        }
                    }
                }
            }
        }
        return $element;
    }

    public function getCategoriesList()
    {
        $structureManager = $this->getService('structureManager');
        $list = $structureManager->getElementsChildren($this->id, ['container'], self::LINK_TYPE_CATEGORY);
        return $list;
    }

    public function getInhabitedCategories()
    {
        if ($this->inhabitedCategories === null) {
            $this->inhabitedCategories = [];
            foreach ($this->getCategoriesList() as $category) {
                if ($category && $category->getShopsList()) {
                    $this->inhabitedCategories[] = $category;
                }
            }
        }
        return $this->inhabitedCategories;
    }

    public function getAllShops()
    {
        $structureManager = $this->getService('structureManager');
        $languagesManager = $this->getService('languagesManager');
        return $structureManager->getElementsByType('shop', $languagesManager->getCurrentLanguageId());
    }

    public function getShopsAlfaIndex()
    {
        if ($this->shopsAlfaIndex === null) {
            $this->shopsAlfaIndex = [];
            $shops = $this->getAllShops();
            $this->shopsAlfaIndex = $this->makeShopsIndex($shops);
        }
        return $this->shopsAlfaIndex;
    }

    protected function makeShopsIndex($shops)
    {
        $result = [];
        $shopNames = [];
        foreach ($shops as $shop) {
            $shopNames[] = mb_strtolower(trim($shop->title));
        }
        array_multisort($shopNames, SORT_ASC, $shops);
        foreach ($shops as $shop) {
            $char = mb_strlen($shop->title) > 0
                ? mb_strtoupper(mb_substr(trim($shop->title), 0, 1))
                : '_';
            if (!isset($result[$char])) {
                $result[$char] = [];
            }
            $result[$char][] = $shop;
        }
        return $result;
    }

    public function getShopIndexLetters()
    {
        return array_keys($this->getShopsAlfaIndex());
    }

    public function filterShops()
    {
        $this->filteredIndex = [];
        $this->filteredCategories = [];
        $controller = controller::getInstance();
        if ($categoryId = $controller->getParameter('category')) {
            $structureManager = $this->getService('structureManager');
            $category = $structureManager->getElementById($categoryId);
            if ($category) {
                $this->filteredCategories = [$category];
            }
        } elseif ($letter = $controller->getParameter('letter')) {
            $this->filteredIndex = $this->getShopsAlfaIndex();
            if ($letter != 'all') {
                $filteredShops = isset($this->filteredIndex[$letter]) ? $this->filteredIndex[$letter] : [];
                $this->filteredIndex = [$letter => $filteredShops];
            }
        } else {
            $this->filteredCategories = $this->getInhabitedCategories();
        }
    }

    public function getFilteredIndex()
    {
        if ($this->filteredIndex === null) {
            $this->filterShops();
        }
        return $this->filteredIndex;
    }

    public function getFilteredCategories()
    {
        if ($this->filteredCategories === null) {
            $this->filterShops();
        }
        return $this->filteredCategories;
    }

    public function getColumnsType()
    {
        return $this->columns;
    }
}