<?php

class paymentMethodsElement extends structureElement
{
    use AutoMarkerTrait;

    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    public $allowedTypes = array(
        'swedbankPaymentMethod',
        'danskebankPaymentMethod',
        'sebPaymentMethod',
        'lhvPaymentMethod',
        'liisiPaymentMethod',
        'estcardPaymentMethod',
        'paypalPaymentMethod',
        'moneybookersPaymentMethod',
        'invoicePaymentMethod',
        'queryPaymentMethod',
        'nordeaPaymentMethod',
        'payseraPaymentMethod',
        'coopPaymentMethod',
        'payanywayPaymentMethod',
        'everypayPaymentMethod',
        'maksekeskusePaymentMethod'
    );
    public $defaultActionName = 'showFullList';
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function getChildrenList($roles = null, $linkType = 'structure', $allowedTypes = null, $useBlackList = false)
    {
        $structureManager = $this->getService('structureManager');

        if ($childrenList = $structureManager->getElementsChildren($this->id)) {
            $sortParameter = array();
            foreach ($childrenList as $element) {
                $sortParameter[] = mb_strtolower($element->title);
            }
            array_multisort($sortParameter, SORT_ASC, $childrenList);
        }

        return $childrenList;
    }
}
