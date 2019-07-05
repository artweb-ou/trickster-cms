<?php

/**
 * @var $genericIcon genericIconElement
 * @var $form ElementForm
 */
class showIconFormShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $genericIcons = $structureManager->getElementsByType('genericIcon');
            if (!empty($genericIcons)) {
                $iconsList = [];
                $linksManager = $this->getService('linksManager');
                $connectedIconsIds = $linksManager->getConnectedIdList($structureElement->id, 'genericIconProduct');
                foreach ($genericIcons as $genericIcon) {
                    $item = [];
                    $item['id'] = $genericIcon->id;
                    $item['title'] = $genericIcon->getTitle();
                    $item['select'] = in_array($genericIcon->id, $connectedIconsIds);
                    $iconsList[] = $item;
                }
            }

            $structureElement->iconsList = $iconsList;
            $structureElement->setTemplate('shared.content.tpl');

            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('action', 'receiveIcon');
            $renderer->assign('form', $structureElement->getForm('icon'));
        }
    }
}