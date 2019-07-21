<?php

/**
 * Class newsListElement
 *
 * @property string $columns
 */
class newsListElement extends menuDependantStructureElement implements ColumnsTypeProvider, ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_newslist';
    protected $allowedTypes = [
        'news',
    ];
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $newsList;
    protected $archiveNewsList;
    protected $pager;
    protected $contentList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['displayAmount'] = 'text';
        $moduleStructure['archiveEnabled'] = 'checkbox';
        $moduleStructure['itemsOnPage'] = 'text';
        $moduleStructure['columns'] = 'text';
        $moduleStructure['hidden'] = 'checkbox';
        $moduleStructure['formRelativesInput'] = 'array';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['cols'] = 'naturalNumber';
        $moduleStructure['captionLayout'] = 'text';

        $moduleStructure['generalOwnerAvatar'] = 'image';
        $moduleStructure['generalOwnerAvatarOriginalName'] = 'fileName';
        $moduleStructure['generalOwnerName'] = 'text';

        $moduleStructure['socMedia_1_Name'] = 'text';
        $moduleStructure['socMedia_1_Icon'] = 'image';
        $moduleStructure['socMedia_1_IconOriginalName'] = 'fileName';
//        $moduleStructure['socMedia_1_Link'] = 'text';

   }

    protected function getTabsList()
    {
        return [
            'showFullList',
            'showForm',
            'showLayoutForm',
            'showLanguageForm',
        ];
    }

    public function getUrlEncoded($url)
    {
        return urlencode($url);
    }

    public function getTranslationSprintf($string_comma) // getTranslationSprintf('news.share_on', $shareTitle)
    {
/*
Jaga %s's
*/
        $final_translation = '';
        $translation_array = explode(',', $string_comma);
        if (count($translation_array)==2) {
            $translationsManager = $this->getService('translationsManager');
            if ($translation_string_format = $translationsManager->getTranslationByName($translation_array[0])) {
                $final_translation = sprintf($translation_string_format, $translation_array[1]);
            }
        }
        return $final_translation;
    }

    public function getNewsList()
    {
        if (is_null($this->newsList)) {
            $this->newsList = [];

            if ($connectedIds = $this->getService('linksManager')->getConnectedIdList($this->id)) {
                $collection = persistableCollection::getInstance('module_news');
                $elementsCount = count((array)$collection->conditionalLoad('distinct(id)', [
                    [
                        'id',
                        'in',
                        $connectedIds,
                    ],
                ], ['date' => 'desc'], [
                    0,
                    $this->displayAmount,
                ], [], true));
                if ($elementsCount) {
                    $page = (int)controller::getInstance()->getParameter('page');
                    $pageItemCount = $this->itemsOnPage ? $this->itemsOnPage : 10;
                    $this->pager = new pager($this->URL, $elementsCount, $pageItemCount, $page, 'page');

                    $limitFields = [
                        $this->pager->startElement,
                        $this->itemsOnPage,
                    ];
                    if ($records = $collection->conditionalLoad('distinct(id)', [
                        [
                            'id',
                            'in',
                            $connectedIds,
                        ],
                    ], ['date' => 'desc'], $limitFields, [], true)
                    ) {
                        $newsIds = [];
                        foreach ($records as &$record) {
                            $newsIds[] = $record['id'];
                        }
                        if ($this->newsList = $this->getService('structureManager')
                            ->getElementsByIdList($newsIds, $this->id)
                        ) {
                            $sort = [];
                            foreach ($this->newsList as &$element) {
                                $sort[] = strtotime($element->date);
                            }
                            array_multisort($sort, SORT_DESC, $this->newsList);
                        }
                    }
                }
            }
        }
        return $this->newsList;
    }

    public function getArchiveNewsList()
    {
        if (is_null($this->archiveNewsList)) {
            $this->archiveNewsList = [];
            if ($connectedIds = $this->getService('linksManager')->getConnectedIdList($this->id)) {
                $elementsCount = count(array_unique($connectedIds));
                if ($elementsCount) {
                    $collection = persistableCollection::getInstance('module_news');

                    $page = (int)controller::getInstance()->getParameter('page');
                    $itemsOnPage = 20;
                    $this->pager = new pager($this->URL . 'id:' . $this->id . '/action:archive/', $elementsCount, $itemsOnPage, $page, 'page');

                    $limitFields = [
                        $this->pager->startElement,
                        $itemsOnPage,
                    ];
                    if ($records = $collection->conditionalLoad('distinct(id)', [
                        [
                            'id',
                            'in',
                            $connectedIds,
                        ],
                    ], ['date' => 'desc'], $limitFields, [], true)
                    ) {
                        $newsIds = [];
                        foreach ($records as &$record) {
                            $newsIds[] = $record['id'];
                        }
                        if ($archiveNewsList = $this->getService('structureManager')
                            ->getElementsByIdList($newsIds, $this->id)
                        ) {
                            $sort = [];
                            foreach ($archiveNewsList as &$element) {
                                $sort[] = strtotime($element->date);
                            }
                            array_multisort($sort, SORT_DESC, $archiveNewsList);

                            $number = $this->pager->startElement;
                            foreach ($archiveNewsList as &$element) {
                                $number++;
                                $this->archiveNewsList[$number] = $element;
                            }
                        }
                    }
                }
            }
        }
        return $this->archiveNewsList;
    }

    public function getPager($archive = false)
    {
        if (!$archive && is_null($this->newsList)) {
            $this->getNewsList();
        } elseif ($archive && is_null($this->archiveNewsList)) {
            $this->getArchiveNewsList();
        }
        return $this->pager;
    }

    public function getParent()
    {
        return $this->getService('structureManager')->getElementsFirstParent($this->id);
    }

    public function getContentList()
    {
        if ($this->contentList === null) {
            $this->contentList = [];

            $linksManager = $this->getService('linksManager');
            if ($connectedNewsIds = $linksManager->getConnectedIdList($this->id, 'structure', 'parent')) {
                $elementsCount = count($connectedNewsIds);
                $elementsOnPage = 30;
                $pager = new pager($this->URL, $elementsCount, $elementsOnPage, (int)controller::getInstance()
                    ->getParameter('page'), 'page');

                $limitFields = [
                    $pager->startElement,
                    $elementsOnPage,
                ];

                if ($records = persistableCollection::getInstance('module_news')->conditionalLoad('distinct(id)', [
                    'id',
                    'in',
                    $connectedNewsIds,
                ], ['date' => 'desc'], $limitFields, [], true)
                ) {
                    $structureManager = $this->getService('structureManager');
                    foreach ($records as &$record) {
                        if ($newsElement = $structureManager->getElementById($record['id'])) {
                            $this->contentList[] = $newsElement;
                        }
                    }
                    $this->getService('renderer')->assign('pager', $pager);
                }
            }
        }
        return $this->contentList;
    }

    public function getParentElementTitle()
    {
        if ($parent = $this->getParent()) {
            return $parent->getTitle();
        }
        return false;
    }

    public function getColumnsType()
    {
        return $this->columns;
    }
}