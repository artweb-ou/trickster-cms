<?php

class translationsExportElement extends structureElement
{
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [];
    public $defaultActionName = 'showFullList';
    public $role = 'container';

    protected function initialize()
    {
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

    public function getTranslations()
    {
        $controller = $this->getService('controller');
        $structureManager = $this->getService('structureManager');

        $start = $controller->getParameter('start');
        $end = $controller->getParameter('end');
        $isAdminTranslations = $controller->getParameter('admin_translations');

        $db = $this->getService('db');
        $sql = $db->table('structure_elements as se')
            ->select([
                'se.id',
                'se_group.id as groupId',
            ])
            ->leftJoin('structure_links as sl', 'se.id', '=', 'sl.childStructureId')
            ->leftJoin('structure_elements as se_group', 'sl.parentStructureId', '=', 'se_group.id');

        if ($start) {
            $sql->where('se.dateModified', '>=', strtotime($start));
        }

        if ($end) {
            $sql->where('se.dateModified', '<', strtotime($end . ' 23:59:59'));
        }

        if ($isAdminTranslations) {
            $sql->where('se.structureType', '=', 'adminTranslation');
            $sql->where('se_group.structureType', '=', 'adminTranslationsGroup');
        } else {
            $sql->where('se.structureType', '=', 'translation');
            $sql->where('se_group.structureType', '=', 'translationsGroup');
        }

        $output = [];
        foreach ($sql->get() as $row) {
            if (!isset($output[$row['groupId']])) {
                $output[$row['groupId']] = [
                    'element' => $structureManager->getElementById($row['groupId']),
                    'translations' => [],
                ];
            }

            $output[$row['groupId']]['translations'][] = $structureManager->getElementById($row['id']);
        }

        return $output;
    }
}


