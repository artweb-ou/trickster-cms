<?php

/*
 * this trait is used for backwards compatibility only.
 * todo: check and remove in 2017
 */

trait deprecatedProductElementTrait
{
    /**
     * @deprecated
     */
    public $selectionRequired = false;
    /**
     * @deprecated
     */
    protected $parametersGroups;
    /**
     * @deprecated - used in older projects templates
     */
    public $selectionsList = [];
    /**
     * @deprecated
     */
    public $parametersList = [];
    /**
     * @deprecated
     */
    public $discountsList = [];
    /**
     * @deprecated
     */
    public $parameterValuesIdIndex;
    /**
     * @deprecated
     */
    protected $primaryParameters;

    /**
     * @return productParametersGroupElement[]
     * @deprecated
     */
    public function getParametersGroups()
    {
        if ($this->parametersGroups === null) {
            $this->parametersGroups = [];
            $groupsParentElements = $this->getConnectedCategories();
            if (!$groupsParentElements) {
                $groupsParentElements = $this->getConnectedCatalogues(true);
            }

            $groupsList = [];
            $groupsIndex = [];
            $sortingIdList = [];

            //todo: replace with index method on parameters group?
            foreach ($groupsParentElements as &$groupsParentElement) {
                if ($groupsParentElement->requested || is_null($sortingIdList)) {
                    $sortingIdList = $groupsParentElement->getParametersIdList();
                }
                $groupElements = $groupsParentElement->getParametersGroups();
                foreach ($groupElements as &$group) {
                    if (!isset($groupsIndex[$group->id])) {
                        $groupsIndex[$group->id] = $group;
                        $groupsList[] = $group;
                    }
                }
            }
            $sortingIdIndex = [];
            if (is_array($sortingIdList)) {
                $sortingIdIndex = array_flip($sortingIdList);
            }
            if (count($groupsList)) {
                $parametersManager = $this->getService('ParametersManager');
                foreach ($groupsList as &$group) {
                    if ($groupParameters = $group->getParametersList()) {
                        $filteredGroupParameters = [];
                        foreach ($groupParameters as &$parameterObject) {
                            //start of preload hack:
                            //if module data is not preloaded, then it's later preloaded many times after object cloning
                            $parameterObject->primary;
                            //end of preload hack

                            if ($values = $parametersManager->getProductParameterValues($this->id, $parameterObject->id)) {
                                if ($parameterObject->structureType == 'productParameter') {
                                    $parameterCopy = clone($parameterObject);
                                    $parameterCopy->value = $values[0];

                                    $this->parametersList[] = $parameterCopy;
                                    $filteredGroupParameters[] = $parameterCopy;
                                } elseif ($parameterObject->structureType == 'productSelection') {
                                    $productOptions = [];
                                    foreach ($parameterObject->getSelectionOptions() as $selectionValueElement) {
                                        if (in_array($selectionValueElement->id, $values)) {
                                            $productOptions[] = $selectionValueElement;
                                        }
                                    }
                                    if ($productOptions) {
                                        $parameterCopy = clone($parameterObject);
                                        $parameterCopy->productOptions = $productOptions;
                                        $this->parametersList[] = $parameterCopy;
                                        if ($parameterCopy->option) {
                                            $this->selectionsList[] = $parameterCopy;
                                        }

                                        $filteredGroupParameters[] = $parameterCopy;

                                        if (count($productOptions) > 1) {
                                            $this->selectionRequired = true;
                                        }
                                    }
                                }
                            }
                        }
                        if ($filteredGroupParameters) {
                            $sort = [];
                            foreach ($filteredGroupParameters as &$parameter) {
                                if (isset($sortingIdIndex[$parameter->id])) {
                                    $sort[] = $sortingIdIndex[$parameter->id];
                                } else {
                                    $sort[] = 0;
                                }
                            }
                            array_multisort($sort, SORT_ASC, $filteredGroupParameters);

                            $groupCopy = clone($group);
                            $groupCopy->parametersList = $filteredGroupParameters;
                            $this->parametersGroups[] = $groupCopy;
                        }
                    }
                }
            }
        }
        return $this->parametersGroups;
    }

    /**
     * @return array
     *
     * @deprecated
     */
    public function getConnectedDiscounts()
    {
        $this->logError('deprecated method getConnectedDiscounts used');
        return $this->getCampaignDiscounts();
    }

    /**
     * @return array
     *
     * @deprecated
     */
    public function getConnectedCampaigns()
    {
        $this->logError('deprecated method getConnectedCampaigns used');
        return $this->getCampaignDiscounts();
    }

    /**
     * @return productParameterElement[]|productSelectionElement[]
     * @deprecated
     */
    public function getPrimaryParameters()
    {
        if ($this->primaryParameters === null) {
            $this->primaryParameters = [];
            foreach ($this->getParametersGroups() as $parametersGroup) {
                foreach ($parametersGroup->getParametersList() as $parameter) {
                    if ($parameter->primary) {
                        $this->primaryParameters[] = $parameter;
                    }
                }
            }
        }
        return $this->primaryParameters;
    }
}
