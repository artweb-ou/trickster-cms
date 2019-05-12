<?php

class showIconFormProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $structureElement->newForm = $structureManager->createElement('galleryImage', 'showForm', $structureElement->id);
        }
        $form = $structureElement->getForm('icon');
        $form->setFormAction($structureElement->newForm->URL);

     //   $contentList = $structureElement->getIconsList();

        $contentList = [];
        $linksManager = $this->getService('linksManager');
        $connectedFieldsIds = $linksManager->getConnectedIdList($structureElement->id, 'productIcon');
        foreach ($connectedFieldsIds as $id) {
            $element = $structureManager->getElementById($id);
            $contentList[] = $element;
        }

        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('contentSubTemplate', 'component.form.tpl');
        $renderer->assign('linkType', 'productIcon');
        $renderer->assign('contentList', $contentList);
        $renderer->assign('form', $form);
    }
}