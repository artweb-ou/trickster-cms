<?php

class ecommercePdfDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->inheritedThemes = ['ecommerceDocument'];
        $this->cssPath = $tricksterPath . 'ecommerce/css/pdf/';
        $this->templatesFolder = $tricksterPath . 'ecommerce/templates/pdf/';
    }

    public function getCssResources()
    {
        if (is_null($this->cssResources)) {
            $this->appendCssResourceFromTheme('all_mixins.less', 'default');
            $this->appendCssResourceFromTheme('reset.less', 'public');
            $this->appendCssResourceFromTheme('module.order.less', 'ecommercePublic');
            $this->loadCssResources();
        }
        return $this->cssResources;
    }

    public function getImageUrl($fileName, $recursion = false, $required = true)
    {
        if (!$result = parent::getImageUrl($fileName, $recursion, $required)) {
            $configurationManager = controller::getInstance()->getConfigManager();
            $publicThemeName = $configurationManager->get('main.publicTheme');
            if ($theme = $this->designThemesManager->getTheme($publicThemeName)) {
                return $theme->getImageUrl($fileName, $recursion, $required);
            }
        }
        return $result;
    }
}

