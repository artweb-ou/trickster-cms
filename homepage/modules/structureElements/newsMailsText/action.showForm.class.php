<?php

use App\Users\CurrentUserService;

class showFormNewsMailsText extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $currentUserService = $this->getService(CurrentUserService::class);
            $user = $currentUserService->getCurrentUser();
            if ($structureElement->from == '') {
                $structureElement->from = $user->firstName . ' ' . $user->lastName;
            }
            if ($structureElement->fromEmail == '') {
                $structureElement->fromEmail = $user->email;
            }

            $structureElement->historyList = [];
            if ($structureElement->hasActualStructureInfo()) {
                if ($groupsElement = $structureManager->getElementByMarker('newsMailsGroups')) {
                    $structureElement->result = $structureManager->getElementsChildren($groupsElement->id);
                }
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}



