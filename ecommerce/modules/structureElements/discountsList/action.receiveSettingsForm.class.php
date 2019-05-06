<?php

class receiveSettingsFormDiscountsList extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            // save data
            $structureElement->prepareActualData();

            $structureElement->persistElementData();

            // save relations
            $linksManager = $this->getService('linksManager');
            // parameters
            $compiledLinks = [];
            if ($elementLinks = $linksManager->getElementsLinks($structureElement->id, 'categoryParameter', 'parent')) {
                foreach ($elementLinks as &$link) {
                    $parameterId = $link->childStructureId;
                    $compiledLinks[$parameterId] = $link;
                }
            }
            $parametersFolder = $structureManager->getElementByMarker('productparameters');
            $parametersGroups = $structureManager->getElementsChildren($parametersFolder->id);
            $parametersList = [];
            foreach ($parametersGroups as &$group) {
                $parametersList = array_merge($parametersList, $structureManager->getElementsChildren($group->id));
            }
            foreach ($parametersList as &$parameter) {
                if (isset($compiledLinks[$parameter->id]) && !in_array($parameter->id, $structureElement->parameters)) {
                    $compiledLinks[$parameter->id]->delete();
                } elseif (!isset($compiledLinks[$parameter->id]) && in_array($parameter->id, $structureElement->parameters)
                ) {
                    $linksManager->linkElements($structureElement->id, $parameter->id, 'categoryParameter');
                }
            }
            $controller->redirect($structureElement->getUrl('showSettingsForm'));
        }
        $structureElement->executeAction("showSettingsForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'priceSortingEnabled',
            'nameSortingEnabled',
            'dateSortingEnabled',
            'brandSortingEnabled',
            'brandFilterEnabled',
            'parameterFilterEnabled',
            'availabilityFilterEnabled',
            'manualSortingEnabled',
            'defaultOrder',
            'parameters',
            'amountOnPageEnabled',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


