<?php

class defaultDesignTheme extends designTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $this->templatesFolder = $pathsManager->getPath('trickster') . 'cms/templates/default/';
        $this->cssPath = $pathsManager->getPath('trickster') . 'cms/css/default/';
        $this->imagesFolder = 'trickster/cms/images/default/';
        $this->imagesPath = ROOT_PATH . $this->imagesFolder;
        $this->javascriptUrl = $controller->baseURL . 'trickster/cms/js/default/';
        $this->javascriptPath = $pathsManager->getPath('trickster') . 'cms/js/default/';
        $this->javascriptFiles = [
            'basic.ajaxManager.js',
            'basic.cookies.js',
            'basic.domHelper.js',
            'basic.TweenLite.CSSPlugin.min.js',
            'basic.TweenLite.ScrollToPlugin.min.js',
            'basic.TweenLite.min.js',
            'basic.eventsManager.js',
            'basic.mouseTracker.js',
            'basic.opacityHandler.js',
            'basic.yass.js',
            'basic.controller.js',
            'basic.storageInterface.js',
            'basic.jsonRequest.js',
            'basic.anchorParameters.js',
            'mixin.domElementMaker.js',
            'mixin.domHelper.js',
            'logics.checkbox.js',
            'logics.debug.js',
            'logics.fileInput.js',
            'logics.radioButton.js',
            'logics.analytics.js',
            'logics.calendarSelector.js',
            'component.checkbox.js',
            'component.debug.js',
            'component.fileInput.js',
            'component.radioButton.js',
            'component.calendarSelector.js',
        ];
    }
}