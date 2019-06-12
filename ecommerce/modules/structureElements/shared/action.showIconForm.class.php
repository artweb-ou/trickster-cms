<?php

class showIconFormShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->newForm = $structureManager->createElement('galleryImage', 'showForm', $structureElement->id);
        }
        $form = $structureElement->getForm('icon');
        $form->setFormAction($structureElement->newForm->URL);

        $contentList = [];
        $linksManager = $this->getService('linksManager');
        $connectedFieldsIds = $linksManager->getConnectedIdList($structureElement->id, $structureElement->structureType . 'Icon');
        foreach ($connectedFieldsIds as $id) {
            if ($element = $structureManager->getElementById($id)){
                $contentList[] = $element;
            }
        }

        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('contentSubTemplate', 'component.form.tpl');
        $renderer->assign('linkType', $structureElement->structureType . 'Icon');
        $renderer->assign('contentList', $contentList);
        $renderer->assign('form', $form);
    }
}