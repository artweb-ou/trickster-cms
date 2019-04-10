<?php

trait ConnectedIconsProviderTrait
{
    protected $connectedIconsIds;
    protected $connectedIcons;

    public function getConnectedIcons()
    {
        if ($this->connectedIcons === null) {
            $this->connectedIcons = [];
            if ($iconIds = $this->getConnectedIconsIds()) {
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                foreach ($iconIds as &$iconId) {
                    if ($iconId && $iconElement = $structureManager->getElementById($iconId)) {
                        $this->connectedIcons[] = $iconElement;
                    }
                }
            }
        }
        return $this->connectedIcons;
    }

    public function getConnectedIconsIds()
    {
        if ($this->connectedIconsIds === null) {
            /**
             * @var linksManager $linksManager
             */
            $linksManager = $this->getService('linksManager');
            $this->connectedIconsIds = $linksManager->getConnectedIdList($this->id, $this->structureType . "Icon", "parent");
        }
        return $this->connectedIconsIds;
    }

    public function updateConnectedIcons($formIcons)
    {
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');

        // check icon links
        if ($connectedIconsIds = $this->getConnectedIconsIds()) {
            foreach ($connectedIconsIds as &$connectedIconId) {
                if (!in_array($connectedIconId, $formIcons)) {
                    $linksManager->unLinkElements($this->id, $connectedIconId, $this->structureType . 'Icon');
                }
            }
        }
        foreach ($formIcons as $selectedIconId) {
            $linksManager->linkElements($this->id, $selectedIconId, $this->structureType . 'Icon');
        }
        $this->connectedIconsIds = null;
    }
}