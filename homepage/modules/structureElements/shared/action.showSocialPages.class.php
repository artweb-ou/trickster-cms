<?php

class showSocialPagesShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');

                if($socialErrorCode = $controller->getParameter('social_error')) {
                    $errorMessage = new SocialErrorMessage($socialErrorCode);
                    $translationsManager = $this->getService('translationsManager');
                    $renderer->assign('socialErrorMessage', $translationsManager->getTranslationByName($errorMessage->getErrorText(), 'adminTranslations'));
                }


                $renderer->assign('contentSubTemplate', 'socialPlugin.showPages.tpl');
            }
        }
    }
}