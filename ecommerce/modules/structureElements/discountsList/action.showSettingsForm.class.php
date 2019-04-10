<?php

class showSettingsFormDiscountsList extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        //        $structureElement->setReplacementElements(array($structureElement));
        $structureElement->categoriesList = [];

        if ($structureElement->final) {
            $linksManager = $this->getService('linksManager');
            if ($parametersFolder = $structureManager->getElementByMarker('productparameters')) {
                $valuesIds = array_flip($linksManager->getConnectedIdList($structureElement->id, 'categoryParameter', 'parent'));
                $structureElement->allParametersGroups = $structureManager->getElementsChildren($parametersFolder->id);
                foreach ($structureElement->allParametersGroups as &$group) {
                    if ($parametersList = $group->getParametersList()) {
                        foreach ($parametersList as &$parameter) {
                            if (isset($valuesIds[$parameter->id])) {
                                $parameter->selected = true;
                            } else {
                                $parameter->selected = false;
                            }
                        }
                    }
                }
            }

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('contentSubTemplate', 'discountsList.settings.tpl');
        }
    }
}