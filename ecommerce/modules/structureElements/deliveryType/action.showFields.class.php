<?php

class showFieldsDeliveryType extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $fieldsIndex = $structureElement->getFieldsIndex();
            if ($fieldsElement = $structureManager->getElementByMarker('basketFields')) {
                $structureElement->fieldsList = $structureManager->getElementsChildren($fieldsElement->id);

                foreach ($structureElement->fieldsList as &$field) {
                    if (isset($fieldsIndex[$field->id])) {
                        $field->selected = true;
                        $field->required = $fieldsIndex[$field->id]->required;
                    } else {
                        $field->selected = false;
                    }
                }
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('fields'));
                $renderer->assign('action', 'receiveFields');
            }
        }
    }
}