<?php

class mallAdminDesignTheme extends designTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'mall/css/admin/';
        $this->templatesFolder = $tricksterPath . 'mall/templates/admin/';
        $this->imagesFolder = 'trickster/mall/images/admin/';
        $this->imagesPath = ROOT_PATH . $this->imagesFolder;
        $this->javascriptUrl = $controller->baseURL . 'trickster/mall/js/admin/';
        $this->javascriptPath = $tricksterPath . 'mall/js/admin/';
        $this->javascriptFiles = [
            'logics.floorMap.js',
            'logics.selectedCampaigns.js',
            'logics.openingHoursGroupForm.js',
            'logics.undoManager.js',
            'component.floorMap.js',
            'component.floorMapPanel.js',
            'component.selectedCampaigns.js',
            'component.openingHoursGroupForm.js',
        ];
    }
}

