<?php

class mallDocumentDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'mall/css/document/';
        $this->templatesFolder = $tricksterPath . 'mall/templates/document/';
    }
}