<?php

class standardDesignDocumentDesignTheme extends designTheme
{
    public function initialize()
    {
        $this->inheritedThemes = array();
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'standardDesign/css/document/';
        $this->templatesFolder = $tricksterPath . 'standardDesign/templates/document/';
    }
}

?>