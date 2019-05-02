<?php

/**
 * Class discountsListElement
 *
 * @property string $columns
 * @property int $priceSortingEnabled;
 * @property int $nameSortingEnabled;
 * @property int $dateSortingEnabled;
 * @property int $brandSortingEnabled;
 * @property int $brandFilterEnabled;
 * @property int $parameterFilterEnabled;
 * @property int $availabilityFilterEnabled;
 * @property int $manualSortingEnabled;
 * @property int $defaultOrder;
 * @property int $parameters;
 * @property int $amountOnPageEnabled;
 *
 */

class discountsListElement extends menuStructureElement implements ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_discounts_list';
    public $defaultActionName = 'show';
    public $role = 'container';
    private $listedDiscounts;
    private $listedDiscountsIds;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['connectAll'] = 'checkbox';
        $moduleStructure['discounts'] = 'numbersArray';
        $moduleStructure['columns'] = 'text';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['hidden'] = 'checkbox';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['productsLayout'] = 'text';
        $moduleStructure['selectedDiscountProductsLayout'] = 'text';

        $moduleStructure['defaultOrder'] = 'text';
        // manul, price, price;desc, title, title;desc
        $moduleStructure['manualSortingEnabled'] = 'text';
        // 0 - inherit, 1 - enabled, 2 - disabled
        $moduleStructure['priceSortingEnabled'] = 'text';
        $moduleStructure['nameSortingEnabled'] = 'text';
        $moduleStructure['dateSortingEnabled'] = 'text';
        $moduleStructure['brandSortingEnabled'] = 'text';
        $moduleStructure['brandFilterEnabled'] = 'text';
        $moduleStructure['parameterFilterEnabled'] = 'text';
        //        $moduleStructure['discountFilterEnabled'] = 'text';
        $moduleStructure['availabilityFilterEnabled'] = 'text';
        $moduleStructure['amountOnPageEnabled'] = 'text';
        $moduleStructure['parameters'] = 'array';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showSettingsForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getDiscounts()
    {
        if ($this->listedDiscounts === null) {
            $this->listedDiscounts = [];
            if ($discountIds = $this->getDiscountsIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($discountIds as &$discountId) {
                    $this->listedDiscounts[] = $structureManager->getElementById($discountId);
                }
            }
        }
        return $this->listedDiscounts;
    }

    public function getDiscountsIds()
    {
        if ($this->listedDiscountsIds === null) {
            $this->listedDiscountsIds = [];
            $linksManager = $this->getService('linksManager');
            $discountIds = $linksManager->getConnectedIdList($this->id, "discountsList", 'parent');

            $shoppingBasketDiscounts = $this->getService('shoppingBasketDiscounts');
            $activeDiscountsIdList = $shoppingBasketDiscounts->getDiscountsIdList();

            foreach ($discountIds as &$discountId) {
                if (in_array($discountId, $activeDiscountsIdList)) {
                    $this->listedDiscountsIds[] = $discountId;
                }
            }
        }
        return $this->listedDiscountsIds;
    }

    public function getColumnsType()
    {
        return $this->columns;
    }
}