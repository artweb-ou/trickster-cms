<?php

class receiveImportCalculationsRule extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();
            if (!$structureElement->title) {
                $titleParts = [];
                $rules = $structureElement->rules;
                foreach ($rules as &$rule) {
                    if ($rule['type'] == 'price') {
                        $titleParts[] = $rule['value'][0] . '-' . $rule['value'][1];
                    } else {
                        foreach ($rule['value'] as &$elementId) {
                            if ($element = $structureManager->getElementById($elementId)) {
                                $titleParts[] = $element->title;
                            }
                        }
                    }
                }
                if ($structureElement->action == 'use_rrp') {
                    $titleParts[] = 'RRP';
                } else {
                    $titleParts[] = '+' . $structureElement->priceModifier;
                }
                $structureElement->title = implode(', ', $titleParts);
            }
            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction('showForm');
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'rules',
            'action',
            'priceModifier',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

