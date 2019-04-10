<?php

class ecommerceDocumentDesignTheme extends designTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'ecommerce/css/document/';
        $this->templatesFolder = $tricksterPath . 'ecommerce/templates/document/';
    }
}

