<?php

/**
 * Class campaignsListElement
 *
 * @property string $columns
 */
class campaignsListElement extends menuStructureElement implements ColumnsTypeProvider
{
    public $dataResourceName = 'module_campaigns_list';
    public $defaultActionName = 'show';
    public $role = 'container';
    private $listedCampaigns;
    private $listedCampaignsIds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['connectAll'] = 'checkbox';
        $moduleStructure['campaigns'] = 'numbersArray';
        $moduleStructure['columns'] = 'text';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['hidden'] = 'checkbox';
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

    public function getTextContent()
    {
        if (is_null($this->textContent)) {
            $this->textContent = $this->title . ".";
            $contentElements = $this->getCampaigns();

            foreach ($contentElements as &$contentElement) {
                $this->textContent .= " " . $contentElement->title . ".";
                if ($contentElement->content) {
                    $this->textContent .= " " . $contentElement->content;
                }
            }
        }
        return $this->textContent;
    }

    public function getCampaigns()
    {
        if ($this->listedCampaigns === null) {
            $this->listedCampaigns = [];
            if ($campaignIds = $this->getCampaignsIds()) {
                $structureManager = $this->getService('structureManager');
                $time = $_SERVER['REQUEST_TIME'];
                foreach ($campaignIds as &$campaignId) {
                    $element = $structureManager->getElementById($campaignId);
                    if (!$element) {
                        continue;
                    }
                    $startDateTime = $element->getDataChunk('startDate')->getStorageValue();
                    $endDateTime = $element->getDataChunk('endDate')->getStorageValue();
                    if (($startDateTime && $startDateTime > $time) || ($endDateTime && $endDateTime <= $time)) {
                        continue;
                    }
                    $this->listedCampaigns[] = $element;
                }
            }
        }
        return $this->listedCampaigns;
    }

    public function getCampaignsIds()
    {
        if ($this->listedCampaignsIds === null) {
            $linksManager = $this->getService('linksManager');
            $this->listedCampaignsIds = $linksManager->getConnectedIdList($this->id, "campaignsList", 'parent');
            $this->listedCampaignsIds = array_reverse($this->listedCampaignsIds);
        }
        return $this->listedCampaignsIds;
    }

    public function getColumnsType()
    {
        return $this->columns;
    }
}


