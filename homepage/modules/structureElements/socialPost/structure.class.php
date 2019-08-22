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
                    $statusText = 'undefined';
                    if (isset($statusesInfo[$pluginElement->id])) {
                        $statusText = $statusesInfo[$pluginElement->id]->status;
                    }

                    $info = [
                        'title' => $pluginElement->title,
                        'id' => $pluginElement->id,
                        'statusText' => $statusText,
                        'pages' => $pluginElement->getPages(),
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
                    $this->statusesInfo[$value->pluginId] = $value;
                }
            }
        }
        return $this->statusesInfo;
    }

    public function updateStatusInfo($pluginId, $status)
    {
        $statusInfo = $this->getStatusesInfo();
        if (isset($statusInfo[$pluginId])) {
            $statusObject = $statusInfo[$pluginId];
        } else {
            $collection = persistableCollection::getInstance('social_publishing_status');
            $statusObject = $collection->getEmptyObject();
            $statusObject->pluginId = $pluginId;
            $statusObject->postId = $this->id;
        }

        $statusObject->status = $status;
        $statusObject->persist();
    }
}

