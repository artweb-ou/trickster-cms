<?php

/**
 * Class newsMailsGroupElement
 *
 * @property string @title
 */
class newsMailsGroupElement extends structureElement
{
    const ADDRESSES_ON_PAGE = 500;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [];
    public $defaultActionName = 'showForm';
    public $role = 'content';
    protected $pager;
    protected $emailAddresses;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['elements'] = 'array';
        $moduleStructure['addAddresses'] = 'array';
    }

    public function getEmailAddresses()
    {
        if (is_null($this->emailAddresses)) {
            $this->emailAddresses = [];
            $structureManager = $this->getService('structureManager');
            $linksManager = $this->getService('linksManager');
            $linksIds = $linksManager->getConnectedIdList($this->id, 'newsmailGroup', 'parent');

            if ($elementsCount = count($linksIds)) {
                $page = (int)controller::getInstance()->getParameter('page');
                $elementsOnPage = self::ADDRESSES_ON_PAGE;
                $this->pager = new pager($this->URL, $elementsCount, $elementsOnPage, $page, 'page');

                $db = $this->getService('db');
                $query = $db->table('module_newsmailaddress')->select('id', 'email')->whereIn('id', $linksIds)
                    ->offset($this->pager->startElement)->limit($elementsOnPage)->orderBy('email', 'asc');
                if ($records = $query->get()) {
                    $addressesIds = array_column($records, 'id');
                    foreach ($addressesIds as &$id) {
                        $this->emailAddresses[] = $structureManager->getElementById($id);
                    }
                }
            }
        }
        return $this->emailAddresses;
    }

    public function getPager()
    {
        if ($this->pager === null) {
            $this->getEmailAddresses();
        }
        return $this->pager;
    }

    public function getChildrenList($roles = null, $linkType = 'structure', $allowedTypes = null, $useBlackList = false)
    {
        return $this->getEmailAddresses();
    }

    public function getActionButtons()
    {
        /**
         * @var translationsManager $translationsManager
         */
        $translationsManager = $this->getService('translationsManager');
        return [
            [
                'action' => 'removeAddresses',
                'text' => $translationsManager->getTranslationByName('newsmailsgroup.removeaddresses', 'adminTranslations'),
                'targetId' => $this->id,
            ],
        ];
    }
}
