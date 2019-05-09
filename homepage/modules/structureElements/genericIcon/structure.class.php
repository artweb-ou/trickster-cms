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
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'image';
        $multiLanguageFields[] = 'originalName';
        $multiLanguageFields[] = 'iconWidth';
    }

    public function getProductsAvailabilityOptions()
    {
        //  return $this->productsAvailabilityTypes;
        return $this->productsAvailabilityOptions('',1);
    }

}