<?php

class serviceElement extends menuStructureElement implements ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_service';
    protected $allowedTypes = ['linkList', 'gallery'];
    public $defaultActionName = 'show';
    public $role = 'container';
    public $feedbackURL = false;
    protected $galleriesList;
    public $feedbackFormsList = false;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['icon'] = 'image';
        $moduleStructure['iconOriginalName'] = 'fileName';
        $moduleStructure['introduction'] = 'html';
        $moduleStructure['content'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['link_1'] = 'text';
        $moduleStructure['link_2'] = 'text';
        $moduleStructure['link_text_1'] = 'text';
        $moduleStructure['link_text_2'] = 'text';
        $moduleStructure['link_1_icon'] = 'image';
        $moduleStructure['link_1_iconOriginalName'] = 'fileName';
        $moduleStructure['link_2_icon'] = 'image';
        $moduleStructure['link_2_iconOriginalName'] = 'fileName';
        $moduleStructure['galleries'] = 'array';
        $moduleStructure['feedbackId'] = 'text';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['formRelativesInput'] = 'array';

        $moduleStructure['layout'] = 'text';
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
            'showLanguageForm',
        ];
    }

    public function getConnectedGalleries()
    {
        if ($this->galleriesList === null) {
            $structureManager = $this->getService('structureManager');
            $linksManager = $this->getService('linksManager');
            $this->galleriesList = [];

            $idList = $linksManager->getConnectedIdList($this->id, 'connectedGallery', 'child');
            foreach ($idList as &$id) {
                if ($gallery = $structureManager->getElementById($id)) {
                    $this->galleriesList[] = $gallery;
                }
            }
        }
        return $this->galleriesList;
    }

    public function getGalleriesLayout()
    {
        if (count($this->getConnectedGalleries()) == 1) {
            return 'details_embedded';
        } else {
            return $this->getCurrentLayout('galleries');
        }
    }
}