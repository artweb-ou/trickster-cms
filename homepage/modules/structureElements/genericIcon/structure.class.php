<?php

    /**
     * Class genericIconElement
     */
class genericIconElement extends structureElement implements ImageUrlProviderInterface
{
    use ConnectedProductsProviderTrait;
    use ConnectedBrandsProviderTrait;
    use ConnectedCategoriesProviderTrait;
    use ConnectedParametersProviderTrait;
    use ImageUrlProviderTrait;
    use ProductsAvailabilityOptionsTrait;
    use ProductIconLocationOptionsTrait;
    use ProductIconRoleOptionsTrait;

    public $dataResourceName = 'module_generic_icon';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['iconProducts'] = 'numbersArray';
        $moduleStructure['iconCategories'] = 'numbersArray';
        $moduleStructure['iconBrands'] = 'numbersArray';
        $moduleStructure['startDate'] = 'date';
        $moduleStructure['endDate'] = 'date';
        $moduleStructure['days'] = 'naturalNumber';
        $moduleStructure['iconWidth'] = 'text';
        $moduleStructure['iconLocation'] = 'naturalNumber';
        $moduleStructure['iconBgColor'] = 'text';
        $moduleStructure['iconTextColor'] = 'text';
        $moduleStructure['iconRole'] = 'naturalNumber';
        $moduleStructure['iconProductAvail'] = 'serializedIndex';
        $moduleStructure['iconProductParameters'] = 'numbersArray';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'image';
        $multiLanguageFields[] = 'originalName';
        $multiLanguageFields[] = 'iconWidth';
    }

    public function getSettingsVariablles($variable){
        $settingsManager = $this->getService('settingsManager');
        $configManager = $this->getService('ConfigManager');
        $variableValue = $settingsManager->getSetting($variable) ?: $configManager->get($variable);

        return $variableValue;
    }

}