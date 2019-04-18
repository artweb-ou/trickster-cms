<?php

class showParametersProduct extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $linksManager = $this->getService('linksManager');

        if ($structureElement->final) {
            $groupsParentElements = $structureElement->getConnectedCategories();
            if (!$groupsParentElements) {
                $groupsParentElements = $structureElement->getConnectedCatalogues();
            }

            //PARAMETERS
            $groupsList = [];
            $groupsIndex = [];
            $categoriesConnectedParametersIds = [];
            foreach ($groupsParentElements as &$groupsParentElement) {
                $groupElements = $groupsParentElement->getParametersGroups();
                foreach ($groupElements as &$group) {
                    if (!isset($groupsIndex[$group->id])) {
                        $groupsIndex[$group->id] = $group;
                        $groupsList[] = $group;
                    }
                }
                if ($connectedParametersIds = array_flip($linksManager->getConnectedIdList($groupsParentElement->id, 'categoryParameter', 'parent'))) {
                    $categoriesConnectedParametersIds += $connectedParametersIds;
                }
            }

            $languagesManager = $this->getService('languagesManager');
            $valuesCollection = persistableCollection::getInstance('module_product_parameter_value');
            $searchFields = ['productId' => $structureElement->id];
            $valuesList = $valuesCollection->load($searchFields);
            $languages = $languagesManager->getLanguagesIdList();

            $formParameters = [];
            foreach ($groupsList as &$parametersGroup) {
                $parametersList = $structureManager->getElementsChildren($parametersGroup->id);
                if (count($parametersList) > 0) {
                    $groupParameters = [];
                    foreach ($parametersList as &$element) {
                        if (!isset($categoriesConnectedParametersIds[$element->id])) {
                            continue;
                        }
                        $parameterInfo = [];
                        $parameterInfo['type'] = $element->structureType;
                        $parameterInfo['id'] = $element->id;
                        if ($element->title) {
                            $parameterInfo['title'] = $element->title;
                        } else {
                            $parameterInfo['title'] = $element->structureName;
                        }
                        $parameterInfo['option'] = $element->option;
                        $parameterInfo['parameterType'] = $element->type;

                        if ($element->structureType == 'productParameter') {
                            if ($element->single != '1') {
                                foreach ($languages as &$languageId) {
                                    $parameterInfo['values'][$languageId] = '';
                                }
                            } else {
                                $parameterInfo['values'][0] = '';
                            }
                            foreach ($valuesList as &$valueObject) {
                                if ($element->id == $valueObject->parameterId) {
                                    $parameterInfo['values'][$valueObject->languageId] = $valueObject->value;
                                }
                            }
                        } elseif ($element->structureType == 'productSelection') {
                            $parameterInfo['options'] = [];
                            $values = $element->getSelectionOptions();
                            foreach ($values as &$selectionValueElement) {
                                $optionInfo = [];
                                $optionInfo['id'] = $selectionValueElement->id;
                                if ($selectionValueElement->title) {
                                    $optionInfo['title'] = $selectionValueElement->title;
                                } else {
                                    $optionInfo['title'] = $selectionValueElement->structureName;
                                }
                                $optionInfo['selected'] = false;
                                $optionInfo['value'] = $selectionValueElement->value;
                                foreach ($valuesList as &$valueObject) {
                                    if ($selectionValueElement->id == $valueObject->value) {
                                        $optionInfo['selected'] = true;
                                        break;
                                    }
                                }
                                $parameterInfo['options'][] = $optionInfo;
                            }
                        }
                        $groupParameters[] = $parameterInfo;
                    }
                    $formParameters[$parametersGroup->id] = $groupParameters;
                }
            }
            $structureElement->setFormValue('formParameters', $formParameters);
            $structureElement->setFormValue('formParametersIndex', $groupsIndex);

            $structureElement->setTemplate('shared.content.tpl');
            $renderer = $this->getService('renderer');
            $renderer->assign('groupsIndex', $groupsIndex);
            $renderer->assign('contentSubTemplate', 'product.parameters.tpl');
        }
    }
}

