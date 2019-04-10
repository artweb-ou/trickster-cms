<?php

class showLanguage extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param languageElement $structureElement
     * @return void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $languagesManager = $this->getService('languagesManager');
        $currentLanguageId = $languagesManager->getCurrentLanguageId();
        if (($structureElement->requested || $structureElement->id == $currentLanguageId) && ($controller->getApplicationName() == 'public' || $controller->getApplicationName() == 'mobile')) {
            $user = $this->getService('user');

            $renderer = $this->getService('renderer');
            $renderer->assign('currentLanguage', $structureElement);
            $user->setStorageAttribute('currentLanguage', $structureElement->id);
            $currentMainMenu = $structureElement->getCurrentMainMenu();
            $renderer->assign('currentMainMenu', $currentMainMenu);

            $renderer->assign('mainMenu', $structureElement->getMainMenuElements());
            if ($currentElement = $structureManager->getCurrentElement()) {
                $renderer->assign('currentElement', $currentElement);
            }
            $renderer->assign('firstPageElement', $structureElement->getFirstPageElement());

            $renderer->assign('currentMainMenu', $structureElement->getCurrentMainMenu());

            $currentLayout = 'layout.default.tpl';
            $renderer->assign('currentLayout', $currentLayout);

            $settingsManager = $this->getService('settingsManager');
            $settings = $settingsManager->getSettingsList($structureElement->id);
            $renderer->assign('settings', $settings);

            //todo: remove global variable and implement same functionality for each required structure element (product, order ...)
            $selectedCurrencyItem = false;
            if (class_exists("CurrencySelector")) {
                $currencySelector = $this->getService('CurrencySelector');
                $selectedCurrencyItem = $currencySelector->getSelectedCurrencyItem();
            }
            $renderer->assign('selectedCurrencyItem', $selectedCurrencyItem);

            $renderer->assign('currentLayout', $currentLayout);
        }
        $structureElement->setViewName('show');
    }
}

