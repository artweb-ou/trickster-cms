<?php

/**
 * Class brandsListElement
 *
 * @property string $columns
 */
class brandsListElement extends structureElement implements ConfigurableLayoutsProviderInterface, MetadataProviderInterface, ColumnsTypeProvider
{
    use ConfigurableLayoutsProviderTrait, MetadataProviderTrait;
    public $dataResourceName = 'module_brands_list';
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $subMenuList;
    protected $brandsList;
    protected $brandsInfo;

    public function getSubMenuList($linkType = 'structure')
    {
        if (is_null($this->subMenuList)) {
            $structureManager = $this->getService('structureManager');
            if ($list = $structureManager->getElementsChildren($this->id, 'container')) {
                $this->subMenuList = $list;
            }
        }
        return $this->subMenuList;
    }

    protected function getTabsList()
    {
        return [
            'showFullList',
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getTextContent()
    {
        if (is_null($this->textContent)) {
            $this->textContent = $this->title . ".";

            if ($this->contentList) {
                foreach ($this->contentList as &$contentElement) {
                    if ($elementContent = $contentElement->getTextContent()) {
                        $this->textContent .= " " . $elementContent;
                    }
                }
            }
        }
        return $this->textContent;
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['brands'] = 'numbersArray';
        $moduleStructure['columns'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['connectAll'] = 'checkbox';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['productsLayout'] = 'text';
    }

    public function getBrandsList()
    {
        if ($this->brandsList === null) {
            $structureManager = $this->getService('structureManager');
            $this->brandsList = $structureManager->getElementsChildren($this->id, 'container', 'brands');
        }
        return $this->brandsList;
    }

    public function getBrandsInfo()
    {
        if ($this->brandsInfo === null) {
            $this->brandsInfo = [];
            $structureManager = $this->getService('structureManager');
            $brandsList = [];
            if ($brandsElement = $structureManager->getElementByMarker('brands')) {
                $brandsList = $structureManager->getElementsFlatTree($brandsElement->id);
            }
            $brandsList = (array)$brandsList === $brandsList ? $brandsList : [];
            $linksManager = $this->getService('linksManager');
            $compiledLinks = $linksManager->getElementsLinksIndex($this->id, 'brands', 'parent');

            foreach ($brandsList as &$brand) {
                $brandItem = [
                    'select' => isset($compiledLinks[$brand->id]),
                    'title' => $brand->getTitle(),
                    'id' => $brand->id,
                ];
                $this->brandsInfo[] = $brandItem;
            }
        }
        return $this->brandsInfo;
    }

    public function getColumnsType()
    {
        return $this->columns;
    }
}