<?php

class showFormOrderField extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $fieldsList = [];
            if ($fieldsElement = $structureManager->getElementByMarker('basketFields')) {
                $fieldsList = $structureManager->getElementsChildren($fieldsElement->id);
                foreach ($fieldsList as &$field) {
                    if($structureElement->fieldId == $field->id) {
                        $field->selected = true;
                    }
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'orderField.form.tpl');
            $renderer->assign('fieldsList', $fieldsList);
        }
    }
}