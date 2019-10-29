<?php

class linkListElement extends menuDependantStructureElement implements ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_linklist';
    protected $allowedTypes = ['linkListItem'];
    public $defaultActionName = 'show';
    public $role = 'content';
    public $linkItems = [];
    public $connectedMenu;
    protected $fixedElement;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hideTitle'] = 'checkbox';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['colorLayout'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['icon'] = 'image';
        $moduleStructure['iconOriginalName'] = 'fileName';
        $moduleStructure['fixedId'] = 'text';
        $moduleStructure['content'] = 'html';
        $moduleStructure['subTitle'] = 'text';
        $moduleStructure['cols'] = 'naturalNumber';
        $moduleStructure['colWidthValue'] = 'naturalNumber';
        $moduleStructure['colWidthUnit'] = 'text';
        $moduleStructure['gapValue'] = 'naturalNumber';
        $moduleStructure['gapUnit'] = 'text';
        $moduleStructure['titlePosition'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getFixedElement()
    {
        if ($this->fixedElement === null && $this->fixedId) {
            $structureManager = $this->getService('structureManager');
            $this->fixedElement = $structureManager->getElementById($this->fixedId);
        }
        return $this->fixedElement;
    }

    public function getLinkListItemStyle()
    {
        $itemPercentageWidth = false;
        $itemFixedWidth = false;
        $itemPadding = false;
        if ($this->cols> 0 && $this->freeImageWidth==0) {
            $itemPercentageWidth = 100 / $this->cols . '%';
        }
        else {
            $itemPercentageWidth = 'auto';
        }
        if ($this->gapValue > -1 && !empty($this->gapUnit)) {
            if ($this->gapValue > 0) {
                if ($this->gapUnit == 'pt') {
                    $itemPadding = ($this->gapValue)/2 / 20 . 'rem';
                }
                else {
                    $itemPadding = ($this->gapValue)/2 . $this->gapUnit;
                }
            }
            else {
                $itemPadding = '0';
            }
        }

        if ($this->colWidthValue > 0 && !empty($this->colWidthUnit)) {
            if ($this->colWidthValue > 0) {
                if ($this->colWidthUnit == 'pt') {
                    $itemFixedWidth = ($this->colWidthValue) / 20 . 'rem';
                }
                else {
                    $itemFixedWidth = ($this->colWidthValue) . $this->colWidthUnit;
                }
            }
        }

        return [
            'itemPercentageWidth' => $itemPercentageWidth,
            'itemPadding' => $itemPadding,
            'itemFixedWidth' => $itemFixedWidth,
        ];
    }


}