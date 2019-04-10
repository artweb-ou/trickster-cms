<?php

class galleryImageElement extends structureElement implements ImageUrlProviderInterface
{
    use ImageUrlProviderTrait;

    public $dataResourceName = 'module_gallery_image';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['description'] = 'html';
        $moduleStructure['alt'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['externalLink'] = 'url';
    }

    public function getTitle()
    {
        if ($this->title) {
            $name = $this->title;
        } elseif ($this->alt) {
            $name = $this->alt;
        } else {
            $name = $this->structureName;
        }
        return $name;
    }

    public function deleteElementData()
    {
        $productOptionsImagesManager = $this->getService('ProductOptionsImagesManager');
        if ($productOptionsImagesManager) {
            $query = $productOptionsImagesManager->queryDb();
            $query->where('image', '=', $this->id);
            $query->delete();
        }
        parent::deleteElementData();
    }
}


