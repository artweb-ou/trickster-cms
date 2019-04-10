<?php

class rssDesignTheme extends designTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->templatesFolder = $tricksterPath . 'cms/templates/rss/';
    }
}