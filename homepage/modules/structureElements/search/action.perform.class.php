<?php

class performSearch extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->executeAction('show');
        if ($this->validated) {
            if (!$structureElement->phrase) {
                $structureElement->phrase = $controller->getParameter('phrase');
            }
            $structureElement->phrase = trim($structureElement->phrase);
            $structureElement->phrase = str_replace('%s%', '/', $structureElement->phrase);;
            $structureElement->result = $structureElement->performSearch($structureElement->phrase);
//            if(!empty($structureElement->phrase)) {
//                $this->getService('searchQueriesManager')->logSearch($structureElement->phrase, 0);
//            }
        }
        $structureElement->setViewName('result');
    }

    public function setValidators(&$validators)
    {
        $validators['phrase'][] = 'notEmpty';
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['phrase'];
    }
}