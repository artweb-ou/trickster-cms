<?php

class ecommerceEmailDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->inheritedThemes = ['ecommerceDocument'];

        $this->cssPath = $tricksterPath . 'ecommerce/css/email/';
        $this->templatesFolder = $tricksterPath . 'ecommerce/templates/email/';
    }
}