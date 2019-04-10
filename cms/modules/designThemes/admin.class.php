<?php

class adminDesignTheme extends designTheme
{
    public function initialize()
    {
        $this->inheritedThemes = ['default'];
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $this->cssPath = $pathsManager->getPath('trickster') . 'cms/css/admin/';
        $this->templatesFolder = $pathsManager->getPath('trickster') . 'cms/templates/admin/';
        $this->imagesFolder = $pathsManager->getPath('trickster') .'cms/images/admin/';
        $this->imagesPath = $this->imagesFolder;
        $this->javascriptUrl = $pathsManager->getPath('trickster') . 'cms/js/admin/';
        $this->javascriptPath = $pathsManager->getPath('trickster') . 'cms/js/admin/';

        $this->javascriptFiles = [
            'logics.addNewElement.js',
            'logics.ajaxSearch.js',
            'logics.ajaxSelect.js',
            'logics.contentFilterForm.js',
            'logics.contentList.js',
            'logics.dropDown.js',
            'logics.formHelper.js',
            'logics.genericForm.js',
            'logics.groupBox.js',
            'logics.privilegesForm.js',
            'logics.tabsBlock.js',
            'logics.translationForm.js',
            'logics.translations.js',
            'logics.chart.js',
            'logics.radioTabs.js',
            'logics.mobile_control.js',
            'logics.animation.js',
            'logics.tableComponent.js',
            'logics.imagePreview.js',
            'logics.button.js',
            'component.tableComponent.js',
            'component.addNewElement.js',
            'component.ajaxItemSearch.js',
            'component.ajaxSearch.js',
            'component.ajaxSelect.js',
            'component.contentFilterForm.js',
            'component.contentList.js',
            'component.dropDown.js',
            'component.formHelper.js',
            'component.genericForm.js',
            'component.groupBox.js',
            'component.headerAjaxSearch.js',
            'component.privilegesForm.js',
            'component.tabsBlock.js',
            'component.translationForm.js',
            'component.chart.js',
            'component.radioTabs.js',
            'component.pager.js',
            'component.animation.js',
            'component.imagePreview.js',
            'component.button.js',
            'triangles.js',
        ];
    }
}