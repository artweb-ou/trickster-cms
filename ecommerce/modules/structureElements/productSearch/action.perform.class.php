<?php

class performProductSearch extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->executeAction('show');
        if ($searchArguments = $structureElement->parseSearchArguments()) {
            $structureElement->results = $structureElement->performSearch($searchArguments);
            $this->getService('renderer')->assign('searchArguments', $searchArguments);
        }
        $structureElement->setViewName('form');
    }
}

