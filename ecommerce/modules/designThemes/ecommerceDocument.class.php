<?php

class ecommerceDocumentDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'ecommerce/css/document/';
        $this->templatesFolder = $tricksterPath . 'ecommerce/templates/document/';
    }
}

