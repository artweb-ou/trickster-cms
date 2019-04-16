<?php

class mallPublicDesignTheme extends designTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'mall/css/public/';
        $this->templatesFolder = $tricksterPath . 'mall/templates/public/';
        $this->imagesFolder = 'mall/images/public/';
        $this->imagesPath = $tricksterPath . $this->imagesFolder;
        $this->javascriptPath = $tricksterPath . 'mall/js/public/';
        $this->javascriptUrl = $controller->baseURL . $pathsManager->getRelativePath('trickster') . 'mall/js/public/';

        $this->javascriptFiles = [
            'basic.raphael.js',
            'logics.roomsMap.js',
            'logics.selectedCampaigns.js',
            'logics.shopCatalogue.js',
            'mixin.draggable.js',
            'mixin.scalable.js',
            'component.roomsMap.js',
            'component.floorMapControls.js',
            'component.selectedCampaignsScroll.js',
            'component.shopCatalogue.js',
            'component.slideGallery.js',
        ];
    }
}
