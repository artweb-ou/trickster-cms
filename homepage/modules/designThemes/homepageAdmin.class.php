<?php

class homepageAdminDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'homepage/css/admin/';
        $this->templatesFolder = $tricksterPath . 'homepage/templates/admin/';
        $this->imagesFolder = 'homepage/images/admin/';
        $this->imagesPath = $tricksterPath . $this->imagesFolder;
        $this->javascriptUrl = $controller->baseURL . $pathsManager->getRelativePath('trickster') . 'homepage/js/admin/';
        $this->javascriptPath = $tricksterPath . 'homepage/js/admin/';
        $this->javascriptFiles = [
            'logics.eventsListForm.js',
            'logics.galleryForm.js',
            'logics.genericIconForm.js',
            'logics.languagesForm.js',
            'logics.latestNewsForm.js',
            'logics.linkListItemForm.js',
            'logics.linkListForm.js',
            'logics.mapForm.js',
            'logics.newsMailForm.js',
            'logics.newsMailsGroupForm.js',
            'logics.newsMailTextSubContent.js',
            'logics.redirectForm.js',
            'logics.selectedEvents.js',
            'logics.serviceForm.js',
            'logics.socialPost.js',
            'logics.submenuListForm.js',
            'logics.eventForm.js',
            'logics.commentForm.js',
            'logics.formSelectOption.js',
            'component.eventForm.js',
            'component.eventsListForm.js',
            'component.galleryForm.js',
            'component.genericIconForm.js',
            'component.languagesForm.js',
            'component.latestNewsForm.js',
            'component.linkListItemForm.js',
            'component.linkListForm.js',
            'component.mapForm.js',
            'component.newsMailForm.js',
            'component.newsMailInfoForm.js',
            'component.newsMailsGroupForm.js',
            'component.newsMailTextSubContentForm.js',
            'component.redirectForm.js',
            'component.selectedEvents.js',
            'component.socialPostForm.js',
            'component.submenuListForm.js',
            'component.commentForm.js',
            'component.formSelectOption.js',
            'component.spoiler.js',
            'logics.visitor.js',
            'logics.selectedEventsForm.js',
            'component.selectedEventsForm.js',
            'logics.showFilters.js',
            'component.showFilters.js',
            'logics.actionsButtons.js',
            'component.actionsButtons.js',
        ];
    }
}