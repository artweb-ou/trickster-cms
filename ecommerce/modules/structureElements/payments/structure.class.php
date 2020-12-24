<?php

class paymentsElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['payment'];
    public $defaultActionName = 'showFullList';
    public $role = 'container';
    protected $paymentsPageList;
    public $pager;
    public $paymentsList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function getPaymentsPage()
    {
        if (is_null($this->paymentsPageList)) {
            $structureManager = $this->getService('structureManager');
            $this->paymentsPageList = [];
            $pagerURL = $this->URL;
            $elementsOnPage = 100;

            $collection = persistableCollection::getInstance('module_payment');
            if ($elementsCount = $collection->countElements('id', [])) {
                $page = 0;
                $controller = controller::getInstance();
                if ($controller->getParameter('page')) {
                    $page = intval($controller->getParameter('page'));
                }

                $pager = new pager($pagerURL, $elementsCount, $elementsOnPage, $page, 'page');
                $this->pager = $pager;

                $orderFields = ['id' => 'desc'];
                $limitFields = [
                    $pager->startElement,
                    $elementsOnPage,
                ];

                $paymentsIdFilter = [];
                if ($records = $collection->conditionalLoad('id', [], $orderFields, $limitFields)) {
                    foreach ($records as $record) {
                        $paymentsIdFilter[] = $record['id'];
                    }
                }

                $this->paymentsPageList = $structureManager->getElementsByIdList($paymentsIdFilter, $this->id, true);

                $sort = [];
                foreach ($this->paymentsPageList as $element) {
                    $sort[] = $element->id;
                }
                array_multisort($sort, SORT_DESC, $this->paymentsPageList);
            }
        }
        return $this->paymentsPageList;
    }
}
