<?php

class defaultDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');

        $this->fontsFolder = 'cms/fonts/';
        $this->fontsPath = $tricksterPath . $this->fontsFolder;
        $this->fontsUrl = $controller->baseURL . $pathsManager->getRelativePath('trickster') . $this->fontsFolder;

        $this->templatesFolder = $tricksterPath . 'cms/templates/default/';
        $this->cssPath = $tricksterPath . 'cms/css/default/';
        $this->imagesFolder = 'trickster/cms/images/default/';
        $this->imagesPath = ROOT_PATH . $this->imagesFolder;
        $this->javascriptUrl = $controller->baseURL . $pathsManager->getRelativePath('trickster') . 'cms/js/default/';
        $this->javascriptPath = $tricksterPath . 'cms/js/default/';
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
            'logics.ajaxSearch.js',
            'logics.ajaxSelect.js',
            'logics.checkbox.js',
            'logics.debug.js',
            'logics.fileInput.js',
            'logics.radioButton.js',
            'logics.analytics.js',
            'logics.calendarSelector.js',
            'component.ajaxSearch.js',
            'component.ajaxSelect.js',
            'component.checkbox.js',
            'component.debug.js',
            'component.fileInput.js',
            'component.radioButton.js',
            'component.calendarSelector.js',
        ];
    }
}