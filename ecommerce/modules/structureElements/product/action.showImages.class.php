<?php

class showImagesProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            if ($structureElement->hasActualStructureInfo()) {
                $structureElement->newForm = $structureManager->createElement('galleryImage', 'showForm', $structureElement->id);
            }
            $form = $structureElement->getForm('images');
            $form->setFormAction($structureElement->newForm->URL);

            $contentlist = [];
            $linksManager = $this->getService('linksManager');
            $connectedFieldsIds = $linksManager->getConnectedIdList($structureElement->id, 'productImage');
            foreach ($connectedFieldsIds as $id) {
                $element = $structureManager->getElementById($id);
                $contentlist[] = $element;
            }

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('linkType', 'productImage');
            $renderer->assign('contentlist', $contentlist);
            $renderer->assign('form', $form);
        }
    }
}