<?php

class latestNewsElement extends menuDependantStructureElement implements ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_latest_news';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $limitingNewsLists;
    protected $newsList;
    protected $pager;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['newsDisplayType'] = 'text';
        $moduleStructure['newsDisplayAmount'] = 'text';
        $moduleStructure['newsManualSearch'] = 'numbersArray';
        $moduleStructure['formNewsListsLimitIds'] = 'numbersArray';
        $moduleStructure['itemsOnPage'] = 'text';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['column'] = 'text';
        $moduleStructure['orderType'] = 'text';
        $moduleStructure['buttonTitle'] = 'text';
        $moduleStructure['buttonUrl'] = 'url';
        $moduleStructure['buttonConnectedMenu'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getNewsListIds()
    {
        $result = [];
        $linksManager = $this->getService('linksManager');
        $connectedNewsListsIds = $linksManager->getConnectedIdList($this->id
            , 'latestNewsNewsList', 'parent');
        if ($connectedNewsListsIds) {
            foreach ($connectedNewsListsIds as &$connectedNewsListsId) {
                $connectedIds = $linksManager->getConnectedIdList($connectedNewsListsId);
                if ($connectedIds) {
                    $result = array_merge($result, $connectedIds);
                }
            }
        } else {
            $result = $this->getNewsListIdsInCurrentLanguage();
        }
        return array_unique($result);
    }

    protected function getNewsListIdsInCurrentLanguage()
    {
        static $result;

        if ($result === null) {
            $result = [];
            $structureManager = $this->getService('structureManager');
            $currentLanguageId = $this->getService('languagesManager')->getCurrentLanguageId();
            $newsListsInLanguage = $structureManager->getElementsByType('newsList', $currentLanguageId);
            if ($newsListsInLanguage) {
                $linksManager = $this->getService('linksManager');
                foreach ($newsListsInLanguage as &$newsListElement) {
                    $connectedIds = $linksManager->getConnectedIdList($newsListElement->id);
                    if ($connectedIds) {
                        $result = array_merge($result, $connectedIds);
                    }
                }
            }
        }
        return $result;
    }

    public function getNewsList($paginated = false)
    {
        if ($this->newsList === null) {
            $this->newsList = $this->newsDisplayType == 'auto'
                ? $this->getLatestNewsList($paginated)
                : $this->getConnectedNewsElements();
        }
        return $this->newsList;
    }

    protected function getLatestNewsList($paginated)
    {
        $result = [];
        if ($this->newsDisplayAmount == 0) {
            return $result;
        }
        $newsIds = $this->getNewsListIds();
        $newsRecords = [];
        if ($newsIds) {
            $conditions = [
                [
                    'id',
                    'in',
                    $newsIds,
                ],
            ];
            $collection = persistableCollection::getInstance('module_news');
            $offset = $totalCount = 0;
            $limit = $this->newsDisplayAmount;
            if ($limit == -1) {
                $limit = $this->itemsOnPage;
            }
            $paginated = $paginated && $this->newsDisplayAmount == -1;
            if ($paginated) {
                $totalCount = count((array)$collection->conditionalLoad(
                    'distinct(id)',
                    $conditions,
                    [],
                    [],
                    [],
                    true
                )
                );
                if ($totalCount > $this->itemsOnPage) {
                    $pageNumber = (int)controller::getInstance()->getParameter('page');
                    $this->pager = new pager($this->URL, $totalCount, $this->itemsOnPage,
                        $pageNumber, 'page');
                    $offset = $this->pager->startElement;
                }
            }
            if (!$paginated || $totalCount > 0) {
                if ($this->orderType == 'rand') {
                    $orderType = 'RAND()';
                } else {
                    $orderType = 'date';
                }

                $newsRecords = $collection->conditionalLoad(
                    'distinct(id)',
                    $conditions,
                    [
                        $orderType => 'desc',
                        'id' => 'desc',
                    ],
                    [
                        $offset,
                        $limit,
                    ],
                    [],
                    true
                );
            }
        }
        $structureManager = $this->getService('structureManager');
        foreach ((array)$newsRecords as $record) {
            if ($newsElement = $structureManager->getElementById($record['id'])) {
                $result[] = $newsElement;
            }
        }
        return $result;
    }

    public function getConnectedNewsElements()
    {
        $result = [];
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        if ($connectedIds = $linksManager->getConnectedIdList($this->id, 'selectedNews', 'parent')) {
            if ($this->orderType == 'rand') {
                shuffle($connectedIds);
            }

            if ($this->itemsOnPage) {
                $connectedIds = array_slice($connectedIds, 0, $this->itemsOnPage);
            }

            foreach ($connectedIds as $connectedId) {
                if ($newsElement = $structureManager->getElementById($connectedId)) {
                    $result[] = $newsElement;
                }
            }
        }

        return $result;
    }

    public function getConnectedNews()
    {
        $connectedNews = [];
        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        $idList = $linksManager->getConnectedIdList($this->id, 'selectedNews', 'parent');

        foreach ($idList as &$newsId) {
            $news = $structureManager->getElementById($newsId);
            $newsInfo = [];
            $newsInfo['title'] = $news->getTitle();
            $newsInfo['id'] = $news->id;
            $newsInfo['select'] = true;
            $connectedNews[] = $newsInfo;
        }

        return $connectedNews;
    }

    public function getLimitingNewsLists()
    {
        if (is_null($this->limitingNewsLists)) {
            $this->limitingNewsLists = [];
            if ($connectedNewsListsIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, 'latestNewsNewsList', 'parent')
            ) {
                $structureManager = $this->getService('structureManager');
                foreach ($connectedNewsListsIds as &$newsListId) {
                    if ($element = $structureManager->getElementById($newsListId)) {
                        if ($parentElement = $structureManager->getElementsFirstParent($element->id)) {
                            $item['title'] = $element->getTitle();
                            $item['select'] = true;
                            $item['id'] = $element->id;
                        }
                        $this->limitingNewsLists[] = $item;
                    }
                }
            }
        }
        return $this->limitingNewsLists;
    }

    public function getPager()
    {
        return $this->pager;
    }

    public function getConnectedButtonMenu() {
        $linksManager = $this->getService('linksManager');
        $buttonConnectedMenuId = $linksManager->getConnectedIdList($this->id, "buttonConnectedMenu", "parent");
        $menus = $this->getDisplayMenusInfo();
        foreach ($menus as &$menu) {
            if($buttonConnectedMenuId[0] === $menu['id']) {
                $menu['select'] = true;
            }
        }
        return $menus;
    }

    public function getButtonConnectedMenuUrl() {
        $linksManager = $this->getService('linksManager');
        $connectedProductsIds = $linksManager->getConnectedIdList($this->id, "buttonConnectedMenu", "parent");
        if(!empty($connectedProductsIds)) {
            $structureManager = $this->getService('structureManager');
            $element = $structureManager->getElementById($connectedProductsIds[0]);
            if($element) {
                return $element->URL;
            }
        }
    }
}