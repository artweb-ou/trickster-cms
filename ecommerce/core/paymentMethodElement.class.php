<?php

/**
 * Class paymentMethodElement
 *
 * @property int sendOrderConfirmation
 * @property int sendAdvancePaymentInvoice
 * @property int sendInvoice
 */
abstract class paymentMethodElement extends structureElement
{
    use specialFieldsElementTrait;
    public $dataResourceName = 'module_paymentmethod';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $specialData;
    protected $connectedDeliveryTypes;

    protected function getTabsList()
    {
        return [
            'showForm',
            'showPaymentSettingsForm',
        ];
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['description'] = 'html';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['data'] = 'text';
        $moduleStructure['deliveryTypesIds'] = 'numbersArray';
        $moduleStructure['sendOrderConfirmation'] = 'checkbox';
        $moduleStructure['sendAdvancePaymentInvoice'] = 'checkbox';
        $moduleStructure['sendInvoice'] = 'checkbox';
        foreach ($this->getSpecialFields() as $fieldName => $specialField) {
            $moduleStructure[$fieldName] = $specialField['format'];
        }
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'description';
        $multiLanguageFields[] = 'introduction';
        $multiLanguageFields[] = 'data';
        foreach ($this->getSpecialFields() as $fieldName => $specialField) {
            if ($specialField['multiLanguage']) {
                $multiLanguageFields[] = $fieldName;
            }
        }
    }

    public function getConnectedDeliveryTypesIds()
    {
        return $this->getService('linksManager')->getConnectedIdList($this->id, 'deliveryTypePaymentMethod', 'child');
    }

    public function getConnectedDeliveryTypes()
    {
        if ($this->connectedDeliveryTypes === null) {
            $this->connectedDeliveryTypes = [];
            if ($connectedIds = $this->getConnectedDeliveryTypesIds()) {
                $structureManager = $this->getService('structureManager');
                foreach ($connectedIds as &$connectedId) {
                    if ($element = $structureManager->getElementById($connectedId)) {
                        $this->connectedDeliveryTypes = $element;
                    }
                }
            }
        }
        return $this->connectedDeliveryTypes;
    }

    public function persistElementData()
    {
        parent::persistElementData();

        if ($this->actionName == 'receive') {
            $linksManager = $this->getService('linksManager');

            if ($connectedIds = $this->getConnectedDeliveryTypesIds()) {
                foreach ($connectedIds as $connectedId) {
                    $linksManager->unLinkElements($connectedId, $this->id, 'deliveryTypePaymentMethod');
                }
            }
            foreach ($this->deliveryTypesIds as $deliveryTypeId) {
                if ($deliveryTypeId) {
                    $linksManager->linkElements($deliveryTypeId, $this->id, 'deliveryTypePaymentMethod');
                }
            }
        }
    }

    public function getDeliveryTypes()
    {
        $deliveryTypesInfo = [];
        $structureManager = $this->getService('structureManager');
        if ($element = $structureManager->getElementByMarker('deliveryTypes')) {
            $deliveryTypes = $element->getChildrenList();
            if ($deliveryTypes) {
                $connectedDeliveryTypesIds = $this->getConnectedDeliveryTypesIds();
                foreach ($deliveryTypes as &$deliveryType) {
                    $item = [];
                    $item['id'] = $deliveryType->id;
                    $item['title'] = $deliveryType->getTitle();
                    $item['select'] = in_array($deliveryType->id, $connectedDeliveryTypesIds);
                    $deliveryTypesInfo[] = $item;
                }
            }
        }
        return $deliveryTypesInfo;
    }

    public function getName()
    {
        return str_replace('PaymentMethodElement', '', get_class($this));
    }

    protected function getConfigPath()
    {
        $path = false;
        $paymentMethodName = $this->getName();
        $pathsManager = $this->getService('PathsManager');
        $fileDirectory = $pathsManager->getRelativePath('paymentMethods');
        if (is_dir($fileDirectory . $paymentMethodName)) {
            $path = $fileDirectory . $paymentMethodName;
        }
        return $path;
    }
}