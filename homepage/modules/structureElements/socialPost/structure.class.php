<?php

class socialPostElement extends structureElement
{
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_social_post';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $publishingInfo;
    protected $statusesInfo;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['linkTitle'] = 'text';
        $moduleStructure['linkDescription'] = 'html';
        $moduleStructure['linkURL'] = 'url';
        $moduleStructure['message'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'text';
        $moduleStructure['replacementImage'] = 'text';
        $moduleStructure['search'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showPublishing',
            'showPrivileges',
        ];
    }

    public function getPublishingInfo()
    {
        if (is_null($this->publishingInfo)) {
            $this->publishingInfo = [];
            $structureManager = $this->getService('structureManager');
            if ($pluginsElement = $structureManager->getElementByMarker('socialPlugins')) {
                $statusesInfo = $this->getStatusesInfo();
                $pluginsList = $structureManager->getElementsChildren($pluginsElement->id);
                foreach ($pluginsList as &$pluginElement) {
                    $pages = [];
                    $pluginStatusText = '';
                    if($pluginElement instanceof socialPluginWithPagesInterface) {
                        foreach($pluginElement->getPages() as $page) {
                            $statusText = 'undefined';
                            if (isset($statusesInfo[$pluginElement->id][$page->id])) {
                                $statusText = $statusesInfo[$pluginElement->id][$page->id]->status;
                            }
                            $page->statusText = $statusText;
                            $pages[] = $page;
                        }
                    }else {
                        if (isset($statusesInfo[$pluginElement->id][0])) {
                            $pluginStatusText = $statusesInfo[$pluginElement->id][0]->status;
                        }
                    }

                    $info = [
                        'title'      => $pluginElement->title,
                        'id'         => $pluginElement->id,
                        'statusText' => $pluginStatusText,
                        'pages'      => $pages,
                        //                        'publishURL' => $this->URL . 'id:' . $this->id . '/action:publish/plugin:' . $pluginElement->id . '/',
                        'publishURL' => $pluginElement->getSocialActionUrl('publish')
                    ];
                    $this->publishingInfo[] = $info;
                }
            }
        }
        return $this->publishingInfo;
    }

    protected function getStatusesInfo()
    {
        if (is_null($this->statusesInfo)) {
            $collection = persistableCollection::getInstance('social_publishing_status');
            $searchFields = ['postId' => $this->id];
            $this->statusesInfo = [];
            if ($list = $collection->load($searchFields)) {
                foreach ($list as &$value) {
                    $this->statusesInfo[$value->pluginId][$value->pageId] = $value;
                }
            }
        }
        return $this->statusesInfo;
    }

    public function updateStatusInfo($pluginId, $status, $pageIds = [])
    {
        $db = $this->getService('db');

        if (!$pageIds) {
            $pageIds = ['0'];
        }

        foreach ($pageIds as $pageId) {
            $data = [
                'postId'   => $this->id,
                'pluginId' => $pluginId,
                'pageId'   => $pageId,
            ];

            $db->table('social_publishing_status')
                ->updateOrInsert($data, ['status' => $status]);
        }
        $this->statusesInfo = null;
    }
}

