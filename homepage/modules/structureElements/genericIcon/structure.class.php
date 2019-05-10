<?php

class genericIconElement extends structureElement implements ImageUrlProviderInterface
{
    use ConnectedProductsProviderTrait;
    use ConnectedBrandsProviderTrait;
    use ConnectedCategoriesProviderTrait;
    use ImageUrlProviderTrait;
    use ProductsAvailabilityOptionsTrait;

    public $dataResourceName = 'module_generic_icon';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['products'] = 'numbersArray';
        $moduleStructure['categories'] = 'numbersArray';
        $moduleStructure['brands'] = 'numbersArray';
        $moduleStructure['startDate'] = 'date';
        $moduleStructure['endDate'] = 'date';
        $moduleStructure['days'] = 'naturalNumber';
        $moduleStructure['iconWidth'] = 'floatNumber';
        $moduleStructure['iconLocation'] = 'naturalNumber';
        $moduleStructure['iconRole'] = 'naturalNumber';
        $moduleStructure['iconProductAvail'] = 'serializedIndex';
        $moduleStructure['icontest'] = 'naturalNumber';
        $moduleStructure['parametersIds'] = 'numbersArray';    
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'image';
        $multiLanguageFields[] = 'originalName';
        $multiLanguageFields[] = 'iconWidth';
    }

    /*
        public function getConnectedParametersIds()
        {
            return $this->getService('linksManager')->getConnectedIdList($this->id, 'selectedProductsParameter', 'pare);
        }
    /*
            public function getSelectionIdsForFiltering()
            {
                $result = [];
                $connectedIds = $this->getConnectedParametersIds();
                if ($connectedIds) {
                    $availableIds = parent::getSelectionIdsForFiltering();
                    if ($availableIds) {
                        $result = array_intersect($connectedIds, $availableIds);
                    }
                }
                return $result;
            }

            public function getConnectedParameters()
            {
                if ($this->connectedParameters === null) {
                    $this->connectedParameters = [];
                    if ($connectedParametersIds = $this->getConnectedParametersIds()) {
                        $this->connectedParameters = $this->getService('structureManager')
                            ->getElementsByIdList($connectedParametersIds, $this->id);
                    }
                }
                return $this->connectedParameters;
            }*/
    public function getParameterSelectionsForFiltering()
    {
        if ($productsListElement = $this->getProductsListElement()) {
            return $productsListElement->getParameterSelectionsForFiltering();
        }
        return false;
    }

    public function getConnectedParametersIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, "productSearchParameter", 'parent');
    }


    public function getProductsAvailabilityOptions()
    {
        //  return $this->productsAvailabilityTypes;
        return $this->productsAvailabilityOptions('',1);
    }

}