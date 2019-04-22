<?php

class installerDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'installer/css/installer/';
        $this->templatesFolder = $tricksterPath . 'installer/templates/installer/';
    }
}

?>