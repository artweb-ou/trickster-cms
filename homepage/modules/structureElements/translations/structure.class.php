<?php

class translationsElement extends TranslationsStructureElement
{
    use SortedChildrenListTrait;
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['translationsGroup'];
    public $defaultActionName = 'showFullList';
    public $role = 'container';

    protected function initialize()
    {
        $this->translationsLanguagesGroup = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['xmlFile'] = 'file';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    protected function getThemeCodes()
    {
        $configManager = $this->getService('ConfigManager');
        $publicThemeName = $configManager->get('main.publicTheme');
        return ['projectEmail', 'projectPdf', 'projectRss', $publicThemeName];
    }
}

