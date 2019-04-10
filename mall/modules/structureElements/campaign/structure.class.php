<?php

class campaignElement extends structureElement
{
    public $dataResourceName = 'module_campaign';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    public $shopURL;
    const LINK_TYPE_SHOP = 'campaigns';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['date'] = 'date';
        $moduleStructure['content'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['shopId'] = 'text';
        $moduleStructure['startDate'] = 'date';
        $moduleStructure['endDate'] = 'date';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'content';
        $multiLanguageFields[] = 'introduction';
    }

    public function getShopUrl()
    {
        $shopURL = false;
        $linksManager = $this->getService('linksManager');
        $structureManager = $this->getService('structureManager');
        $list = $linksManager->getConnectedIdList($this->id, 'campaigns', 'child');
        foreach ($list as &$id) {
            if ($shop = $structureManager->getElementById($id)) {
                $shopURL = $shop->URL;
                break;
            }
        }
        return $shopURL;
    }

    public function getConnectedShopId()
    {
        $result = 0;
        $linksManager = $this->getService('linksManager');
        foreach ($linksManager->getConnectedIdList($this->id, self::LINK_TYPE_SHOP, "child", true) as $result) {
            break;
        }
        return $result;
    }

    public function getConnectedShop()
    {
        $result = null;
        $structureManager = $this->getService('structureManager');
        $shopId = $this->getConnectedShopId();
        if ($shopId) {
            $result = $structureManager->getElementById($shopId);
        }
        return $result;
    }
}
