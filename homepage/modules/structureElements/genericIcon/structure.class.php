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

    /**
     * @param $moduleStructure
     */
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
        $moduleStructure['parametersIds'] = 'numbersArray';
        $moduleStructure['parameters'] = 'numbersArray';
    }

    /**
     * @param $multiLanguageFields
     */
    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'image';
        $multiLanguageFields[] = 'originalName';
        $multiLanguageFields[] = 'iconWidth';
    }

    public function getProductSelectionParameters()
    {
        $selectionParameters = [];
        $structureManager = $this->getService('structureManager');
        if ($productSelectionElements = $structureManager->getElementsByType('productSelection')) {
            $connectedProductSelectionIds = $this->getConnectedProductSelectionIds();
            foreach ($productSelectionElements as &$productSelectionElement) {
                $selectionId = $productSelectionElement->id;
                $selectionParameters[$selectionId] = [
                    'id' => $productSelectionElement->id,
                    'title' => $productSelectionElement->title,
                    'structureName' => $productSelectionElement->structureName,
                    'select' => in_array($productSelectionElement->id, $connectedProductSelectionIds),
                ];
                //                $productSelectionOptions = $structureManager->getElementsChildren($selectionId);
                //
                //                foreach ($productSelectionOptions as &$productSelectionOption) {
                //                    $selectionParameters[$selectionId]['options'][] = array(
                //                        'id'        => $productSelectionOption->id,
                //                        'title'     => $productSelectionOption->title,
                //                        'select'    => in_array($productSelectionOption->id, $connectedProductSelectionIds)
                //                    );
                //                }
            }
        }
        return $selectionParameters;
    }

    public function getConnectedProductSelectionIds()
    {
        return $this->getService('linksManager')
            ->getConnectedIdList($this->id, 'selectedProductsProductSelection', 'parent');
    }
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

    public function getConnectedParametersIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'selectedProductsParameter', 'parent');
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
    }




    /**
     * @return array
     */
    public function getProductsAvailabilityOptions()
    {
        //  return $this->productsAvailabilityTypes;
        return $this->productsAvailabilityOptions('',1);
    }

}