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

        $contentlist = [];
        $linksManager = $this->getService('linksManager');
        $connectedFieldsIds = $linksManager->getConnectedIdList($structureElement->id, $structureElement->structureType.'Icon');
        foreach ($connectedFieldsIds as $id) {
            $element = $structureManager->getElementById($id);
            $contentlist[] = $element;
        }

        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('contentSubTemplate', 'component.form.tpl');
        $renderer->assign('linkType', $structureElement->structureType.'Icon');
        $renderer->assign('contentlist', $contentlist);
        $renderer->assign('form', $form);
    }
}