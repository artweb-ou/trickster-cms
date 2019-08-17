<?php

/**
 * Class genericIconElement
 *
 * @property int $iconRole
 * @property int $days
 * @property string[] $iconProductAvail
 * @property string $startDate
 * @property string $endDate
 * @property string $iconWidth
 * @property string $iconWidthOnProduct
 * @property integer $applicableToAllProducts
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

    /**
     * @param $moduleStructure
     */
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
        $moduleStructure['iconWidthOnProduct'] = 'text';
        $moduleStructure['iconLocation'] = 'naturalNumber';
        $moduleStructure['iconBgColor'] = 'text';
        $moduleStructure['iconTextColor'] = 'text';
        $moduleStructure['iconRole'] = 'naturalNumber';
        $moduleStructure['applicableToAllProducts'] = 'checkbox';
        $moduleStructure['iconProductAvail'] = 'serializedIndex';
        $moduleStructure['iconProductParameters'] = 'numbersArray';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'image';
        $multiLanguageFields[] = 'originalName';
        $multiLanguageFields[] = 'iconWidth';
        $multiLanguageFields[] = 'iconWidthOnProduct';
    }

    public function getSettingsVariablles($variable)
    {
        $settingsManager = $this->getService('settingsManager');
        $configManager = $this->getService('ConfigManager');
        $variableValue = $settingsManager->getSetting($variable) ?: $configManager->get($variable);

        return $variableValue;
    }

}