<?php

class selectedCampaignsElement extends menuDependantStructureElement
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_selectedcampaigns';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $connectedCampaignsIds;
    protected $campaignsToDisplay;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['connectedCampaigns'] = 'numbersArray';
        $moduleStructure['displayMenus'] = 'array';
        $moduleStructure['receivedCampaignsIds'] = 'array'; // temporary
        $moduleStructure['connectAll'] = 'checkbox';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showLayoutForm',
            'showFilters',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getCampaignsToDisplay()
    {
        if ($this->campaignsToDisplay === null) {
            $this->campaignsToDisplay = [];
            $connectedCampaignsIds = $this->getConnectedCampaignsIds();
            if ($connectedCampaignsIds) {
                $structureManager = $this->getService('structureManager');
                $time = $_SERVER['REQUEST_TIME'];
                foreach ($connectedCampaignsIds as &$campaignId) {
                    $element = $structureManager->getElementById($campaignId);
                    if (!$element) {
                        continue;
                    }
                    $startDateTime = $element->getDataChunk('startDate')->getStorageValue();
                    $endDateTime = $element->getDataChunk('endDate')->getStorageValue();
                    if (($startDateTime && $startDateTime > $time) || ($endDateTime && $endDateTime <= $time)) {
                        continue;
                    }
                    $this->campaignsToDisplay[] = $element;
                    $campaignsDates[] = strtotime($element->dateCreated);
                }
                array_multisort($campaignsDates, SORT_DESC, $this->campaignsToDisplay);
            }
        }
        return $this->campaignsToDisplay;
    }

    public function getAvailableCampaigns()
    {
        $result = [];
        $availableCampaigns = [];
        $structureManager = $this->getService('structureManager');
        $campaignsFolder = $structureManager->getElementByMarker('campaigns');
        if ($campaignsFolder) {
            $result = $structureManager->getElementsFlatTree($campaignsFolder->id);
            if ($connectedIds = $this->getConnectedCampaignsIds()) {
                foreach ($result as &$campaignElement) {
                    $item = [];
                    $item['id'] = $campaignElement->id;
                    $item['structureName'] = $campaignElement->structureName;
                    $item['title'] = $campaignElement->title;
                    $item['select'] = in_array($campaignElement->id, $connectedIds);
                    $availableCampaigns[] = $item;
                }
            }
        }
        return $availableCampaigns;
    }

    public function getConnectedCampaignsIds()
    {
        if (is_null($this->connectedCampaignsIds)) {
            $this->connectedCampaignsIds = $this->getService('linksManager')
                ->getConnectedIdList($this->id, "selectedCampaignsCampaign", "parent");
        }
        return $this->connectedCampaignsIds;
    }
}
