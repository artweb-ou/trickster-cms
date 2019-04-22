<?php

class homepageRssDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->templatesFolder = $tricksterPath . 'homepage/templates/rss/';
    }
}