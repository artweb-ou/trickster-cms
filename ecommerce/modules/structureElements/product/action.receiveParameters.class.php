<?php

class receiveParametersProduct extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            //persist parameters values
            //prepare parameters elements index
            $parametersIndex = [];
            $groupsParentElements = $structureElement->getConnectedCategories(true);
            if (!$groupsParentElements) // TODO: do something about this workaround
            {
                $groupsParentElements = $structureElement->getConnectedCatalogues(true);
            }

            foreach ($groupsParentElements as &$groupsParentElement) {
                $groupElements = $groupsParentElement->getParametersGroups();
                foreach ($groupElements as &$group) {
                    if ($parametersList = $group->getParametersList()) {
                        foreach ($parametersList as &$parameter) {
                            $parametersIndex[$parameter->id] = $parameter;
                        }
                    }
                }
            }

            $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
            $searchFields = ['productId' => $structureElement->id];
            $valuesList = $valuesCollection->load($searchFields);

            foreach ($structureElement->formParameters as $parameterId => $parameterInfo) {
                if (isset($parametersIndex[$parameterId])) {
                    if ($parametersIndex[$parameterId]->structureType == 'productParameter') {
                        foreach ($parameterInfo as $languageId => &$value) {
                            $selectedValueObject = false;
                            foreach ($valuesList as $valuesKey => &$valueObject) {
                                if ($valueObject->parameterId == $parameterId && $valueObject->languageId == $languageId) {
                                    unset($valuesList[$valuesKey]);
                                    $selectedValueObject = $valueObject;
                                }
                            }
                            if ($value != '') {
                                if (!$selectedValueObject) {
                                    $selectedValueObject = $valuesCollection->getEmptyObject();
                                    $selectedValueObject->parameterId = $parameterId;
                                    $selectedValueObject->languageId = $languageId;
                                    $selectedValueObject->productId = $structureElement->id;
                                }
                                $selectedValueObject->value = htmlspecialchars($value, ENT_QUOTES);
                                $selectedValueObject->persist();
                            } elseif ($selectedValueObject) {
                                $selectedValueObject->delete();
                            }
                        }
                    } elseif ($parametersIndex[$parameterId]->structureType == 'productSelection') {
                        foreach ($parameterInfo as $optionValueId) {
                            $selectedValueObject = false;
                            if ($optionValueId != '') {
                                foreach ($valuesList as $valuesKey => &$valueObject) {
                                    if ($valueObject->parameterId == $parameterId && $valueObject->value == $optionValueId) {
                                        unset($valuesList[$valuesKey]);
                                        $selectedValueObject = $valueObject;
                                    }
                                }

                                if (!$selectedValueObject) {
                                    $selectedValueObject = $valuesCollection->getEmptyObject();
                                    $selectedValueObject->parameterId = $parameterId;
                                    $selectedValueObject->productId = $structureElement->id;
                                    $selectedValueObject->languageId = 0;
                                }
                                $selectedValueObject->value = $optionValueId;
                                $selectedValueObject->persist();
                            }
                        }
                    }
                }
            }
            foreach ($valuesList as &$valueObject) {
                $valueObject->delete();
            }

            $controller->redirect($structureElement->URL);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'formParameters',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

