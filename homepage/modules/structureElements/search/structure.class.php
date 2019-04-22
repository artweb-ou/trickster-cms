<?php

class searchElement extends menuDependantStructureElement
{
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_search';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'container';
    const DEFAULT_PAGE_SIZE = 50;
    protected $searchCutLength = 200;
    public $results;
    protected $pager;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['phrase'] = 'text';

        $moduleStructure['bAjaxSearch'] = 'text';
    }

    /**
     * remove after 01.2020
     * @return array
     * @deprecated - replace with getSearchTypes
     */
    public function getTypes()
    {
        $this->logError('deprecated method used: searchElement::getTypes');
        $configManager = $this->getService('ConfigManager');

        $types = $this->getSearchTypes();
        if ($deprecatedTypes = $configManager->get('main.allowedPublicSearchTypes')) {
            $this->logError('deprecated config row used: main.allowedPublicSearchTypes');
            $types = array_merge($types, explode(',', $deprecatedTypes));
        }
        if (!$types) {
            $this->logError('deprecated search types used, use config instead');

            $types = [
                "product",
                "category",
                "article",
                "news",
                "gallery",
                "production",
            ];
        }
        return $types;
    }

    /**
     * remove after 01.2020
     * @return string
     * @deprecated - replace with getSearchTypesString
     */
    public function getTypesString()
    {
        return $this->getSearchTypesString();
    }

    public function performSearch($phrase)
    {
        $configManager = $this->getService('ConfigManager');
        $pageSize = self::DEFAULT_PAGE_SIZE;
        $pageNumber = (int)controller::getInstance()->getParameter('page');
        $offset = max(0, $pageNumber - 1) * $pageSize;
        $search = new Search();
        $search->setInput($phrase);
        $search->setLimit($pageSize);
        $search->setOffset($offset);
        $search->setPartialMatching(true);
        $search->setContentMatching(true);
        $search->setTypes($this->getSearchTypes());
        $result = $search->getResult();
        if ($result->count) {
            // log this search and append the search ID to result urls for tracking
            $searchId = $this->getService('searchQueriesManager')->logSearch($phrase, $result->count);
            $designThemesManager = $this->getService('DesignThemesManager');
            $currentTheme = $designThemesManager->getCurrentTheme();
            foreach ($result->sets as $set) {
                if ($currentTheme->templateExists('search.set.' . $set->type . '.tpl')) {
                    $set->template = 'search.set.' . $set->type . '.tpl';
                }

                foreach ($set->elements as $setElement) {
                    $searchLayout = $configManager->get('layouts.' . $setElement->structureType . '.search.default');
                    if ($searchLayout && $currentTheme->templateExists($setElement->getTemplate($searchLayout))) {
                        $setElement->setViewName($searchLayout);
                        $setElement->setTemplate($setElement->structureType . '.' . $searchLayout . '.tpl');
                    }
                }
            }
            foreach ($result->elements as $foundElement) {
                if ($foundElement) {
                    if ($currentTheme->templateExists($foundElement->getTemplate('search'))) {
                        $foundElement->setViewName('search');
                        $this->formatSearchResultElement($foundElement, [
                            'title',
                            'content',
                            'introduction',
                        ], $phrase);
                    }
                    $foundElement->URL .= "qid:" . $searchId . "/";
                }
            }
            $this->pager = new pager($this->URL . 'action:perform/id:' . $this->id . '/phrase:' . $this->phrase . '/'
                , $result->count, $pageSize, $pageNumber, 'page');
        }
        return $result;
    }

    protected function setPhraseCondition($phraseParts, $columnData, &$conditions)
    {
        foreach ($phraseParts as &$phrasePart) {
            if ($phrasePart) {
                $condition["column"] = $columnData;
                $condition["action"] = "like";
                $condition["argument"] = '%%' . $phrasePart . '%%';
                $conditions[] = $condition;
            }
        }
    }

    protected function formatSearchResultElement(&$element, $fields, $phrase)
    {
        $element->searchContent = $element->content;
        $element->searchTitle = $element->title;

        if (!is_array($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as &$field) {
            if (!($value = $element->$field)) {
                continue;
            }

            $textValue = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');

            if (($position = mb_stripos($textValue, $phrase)) === false) {
                continue;
            }
            $phraseLength = mb_strlen($phrase);

            if ($field == 'title') {
                $searchTitle = htmlspecialchars($textValue, ENT_QUOTES, 'UTF-8');
                $searchTitle = preg_replace('/' . preg_quote($phrase, '/') . '/', '<span class="search_emphasized">' . $phrase . '</span>', $searchTitle);
                $element->searchTitle = $searchTitle;
            } else {
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
                $element->searchContent = $searchContent;
            }
        }
    }

    public function getPager()
    {
        return $this->pager;
    }
}


