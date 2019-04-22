<?php

class shoppingBasketDataDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $projectPath = $controller->getProjectPath();
        $this->javascriptPath = $projectPath . 'js/shoppingBasketData/';
        $configManager = $controller->getConfigManager();
        $config = $configManager->getConfig('main');
        $scripts = [];
        if ($config->get('dpdEnabled') !== false) {
            $scripts[] = 'dpd.js';
        }
        if ($config->get('post24Enabled') !== false) {
            $scripts[] = 'post24.js';
        }
        if ($config->get('smartPostEnabled') !== false) {
            $scripts[] = 'smartpost.js';
        }
        $this->javascriptFiles = $scripts;
    }
}

