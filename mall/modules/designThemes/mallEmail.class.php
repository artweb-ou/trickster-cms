<?php

class mallEmailDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $this->inheritedThemes = ['mallDocument'];
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'mall/css/email/';
        $this->templatesFolder = $tricksterPath . 'mall/templates/email/';
    }
}