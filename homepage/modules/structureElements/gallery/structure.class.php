<?php

class galleryElement extends menuDependantStructureElement implements ConfigurableLayoutsProviderInterface
{
    use GalleryInfoProviderTrait;
    use ConfigurableLayoutsProviderTrait;
    use ImagesElementTrait;
    //todo: remove this trait usage after PHP7.3 fix on Zone
    use CacheOperatingElement;

    public $dataResourceName = 'module_gallery';
    protected $allowedTypes = ['galleryImage'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $columnWidth;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hideTitle'] = 'checkbox';
        $moduleStructure['content'] = 'html';
        $moduleStructure['listLayout'] = 'text';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['columns'] = 'naturalNumber';
        $moduleStructure['gapValue'] = 'naturalNumber';
        $moduleStructure['gapUnit'] = 'text';
        $moduleStructure['clickDisable'] = 'checkbox';
        $moduleStructure['freeImageWidth'] = 'checkbox';
        $moduleStructure['captionLayout'] = 'text';
        $moduleStructure['slideType'] = 'text';
        
        $moduleStructure['colorLayout'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
            'showImages'
        ];
    }

    public function getColumnWidth()
    {
        if ($this->columnWidth === null) {
            $this->columnWidth = 24;
            if ($this->columns > 0) {
                $this->columnWidth = 100 / $this->columns - 1;
            }
        }
        return $this->columnWidth;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getFigureStyle()
    {
        $figureWidth = false;
        $figurePadding = false;
        if ($this->columns > 0 && $this->freeImageWidth==0) {
            $figureWidth = 100 / $this->columns . '%';
        }
        else {
            $figureWidth = 'auto';
        }
        if ($this->gapValue > -1 && !empty($this->gapUnit)) {
            if ($this->gapValue > 0) {
                $figurePadding = ($this->gapValue)/2 . $this->gapUnit;
            }
            else {
                $figurePadding = '0';
            }
        }
        
        return [
            'figureWidth' => $figureWidth,
            'figurePadding' => $figurePadding,
        ];
    }

    public function getSlideType()
    {
        if ($this->slideType == '') {
            return 'slide';
        } else {
            return $this->slideType;
        }
    }

    public function getImagesLinkType()
    {
        //legacy-support, use trait's method instead
        return 'structure';
    }
    public function getJsonInfo($galleryOptions = [], $imagePresetBase = 'gallery')
    {
        return $this->getGalleryJsonInfo($galleryOptions, $imagePresetBase);
    }
}