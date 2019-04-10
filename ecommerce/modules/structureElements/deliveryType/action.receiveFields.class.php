<?php

class receiveFieldsDeliveryType extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param deliveryTypeElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            $structureElement->persistElementData();

            // update form fields
            $fieldsIndex = $structureElement->getFieldsIndex();
            $fieldsCollection = persistableCollection::getInstance('delivery_type_field');
            foreach ($structureElement->fields as $fieldId => $required) {
                if (isset($fieldsIndex[$fieldId])) {
                    $fieldObject = $fieldsIndex[$fieldId];
                    unset($fieldsIndex[$fieldId]);
                } else {
                    $fieldObject = $fieldsCollection->getEmptyObject();
                    $fieldObject->fieldId = $fieldId;
                    $fieldObject->deliveryTypeId = $structureElement->id;
                }
                $fieldObject->required = $required;
                $fieldObject->persist();
            }
            //delete obsolete records
            foreach ($fieldsIndex as &$fieldRecord) {
                $fieldRecord->delete();
            }
            $linksManager = $this->getService('linksManager');
            $linksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'deliveryTypeField', 'parent');
            foreach ($structureElement->fields as $fieldId => $required) {
                if (is_numeric($fieldId)) {
                    $linksManager->linkElements($structureElement->id, $fieldId, 'deliveryTypeField');
                }
                unset($linksIndex[$fieldId]);
            }
            foreach ($linksIndex as &$link) {
                $link->delete();
            }

            $controller->redirect($structureElement->getUrl('showFields'));
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'fields',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['structureName'][] = 'notEmpty';
    }
}