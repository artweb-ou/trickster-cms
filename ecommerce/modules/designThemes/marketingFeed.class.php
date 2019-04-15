<?php

class marketingFeedDesignTheme extends designTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->templatesFolder = $tricksterPath . 'ecommerce/templates/marketingFeed/';
    }
}