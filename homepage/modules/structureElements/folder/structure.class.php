<?php

/**
 * Class folderElement
 *
 * @property string $title
 * @property string $columns
 * @property string $image
 * @property string $originalName
 */
class folderElement extends menuDependantStructureElement implements ConfigurableLayoutsProviderInterface, ColumnsTypeProvider
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_folder';
    public $defaultActionName = 'show';
    public $role = 'container';

    protected function getTabsList()
    {
        return [
            'showFullList',
            'showForm',
            'showSeoForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
            'showLanguageForm',
        ];
    }

    public function getTextContent()
    {
        if (is_null($this->textContent)) {
            $this->textContent = "";
            if ($contentElements = $this->getContentList()) {
                // TODO add getContentList method
                foreach ($contentElements as &$contentElement) {
                    if ($contentElement->title) {
                        $this->textContent .= $contentElement->title . ".";
                    }

                    if ($contentElement->introduction) {
                        $this->textContent .= " " . $contentElement->introduction . " ";
                        $this->textContent .= $contentElement->content ? " " . $contentElement->content : "";
                    } else {
                        $this->textContent .= $contentElement->content ? " " . $contentElement->content : " " . $contentElement->title;
                    }
                }
            }
        }
        return $this->textContent;
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['metaTitle'] = 'text';
        $moduleStructure['h1'] = 'text';
        $moduleStructure['metaDescription'] = 'text';
        $moduleStructure['canonicalUrl'] = 'url';
        $moduleStructure['metaDenyIndex'] = 'checkbox';
        $moduleStructure['columns'] = 'text';
        $moduleStructure['externalUrl'] = 'url';

        $moduleStructure['formRelativesInput'] = 'array';
        $moduleStructure['hidden'] = 'checkbox';

        $moduleStructure['layout'] = 'text';
        $moduleStructure['colorLayout'] = 'text';
    }

    /**
     * @return structureElement[]
     * @deprecated historically, use getContentElements instead
     */
    public function getContentList()
    {
        return $this->getContentElements('structure');
    }

    public function getContentElements($types = null)
    {
        return $this->getChildrenList('content', $types, null, true);
    }

    public function getParent()
    {
        return $this->getService('structureManager')->getElementsFirstParent($this->id);
    }

    public function getSubMenuList($linkType = [])
    {
        $subMenus = [];

        $structureManager = $this->getService('structureManager');
        $childrenList = $structureManager->getElementsChildren($this->id, 'container', $linkType, null, true);

        foreach ($childrenList as &$child) {
            if (!$child->hidden) {
                $subMenus[] = $child;
            }
        }
        return $subMenus;
    }

    public function getColumnsType()
    {
        return $this->columns;
    }

    /**
     * Get allowed children structure elements type according to settings, current user's privileges and selected type
     *
     * @param string $currentAction
     * @return string[]
     */
    public function getAllowedTypes($currentAction = 'showFullList')
    {
        if ($this->allowedTypes === null) {
            /**
             * @var ConfigManager $configManager
             */
            $configManager = $this->getService('ConfigManager');
            $this->allowedTypes = $configManager->getMerged('folder-allowedTypes.content');
        }
        return parent::getAllowedTypes($currentAction);
    }

}