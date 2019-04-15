<?php

class rootElement extends structureElement
{
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [
        'root',
        'catalogues',
        'marketing',
        'events',
        'shoppingBasketSettings',
        'system',
        'userSystem',
        'mall',
    ];
    public $defaultActionName = 'show';
    public $role = 'container';

    protected function getTabsList()
    {
        if ($this->marker == 'public_root') {
            return [
                'showFullList',
                'showForm',
                'showPositions',
                'showPrivileges',
            ];
        } else {
            return [
                'showDashboard',
                'showList',
                'showForm',
                'showPositions',
                'showPrivileges',
            ];
        }
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['elements'] = 'array';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function getAllowedTypes($childCreationAction = 'showForm')
    {
        if ($this->isAdminRoot() === false) {
            $this->allowedTypes = ['language'];
        }
        return parent::getAllowedTypes($childCreationAction);
    }

    public function isAdminRoot()
    {
        $adminMarker = $this->getService('ConfigManager')
            ->get('main.rootMarkerAdmin');
        return $this->marker == $adminMarker;
    }

    public function getFormActionURL($type = null)
    {
        $controller = controller::getInstance();
        if ($contentType = $controller->getParameter('view')) {
            return $this->URL . 'view:' . $contentType . '/';
        }

        return $this->URL;
    }
}