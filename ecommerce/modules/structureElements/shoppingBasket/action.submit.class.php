<?php

class submitShoppingBasket extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;
        if (!$structureElement->shoppingBasket->getProductsList()) {
            // session expired
            $controller->redirect($structureElement->URL);
        }
        $renderer = $this->getService('renderer');
        $renderer->assign('shoppingBasket', $structureElement);

        $formErrors = $structureElement->getFormErrors();
        $formData = $structureElement->getFormData();
        $deliveryType = $shoppingBasket->getSelectedDeliveryType();
        if ($customFields = $structureElement->getCustomFieldsList()) {
            foreach ($customFields as &$field) {
                $fieldName = $field->fieldName;
                $deliveryType->setFieldValue($fieldName, $formData[$fieldName]);
                if (isset($formErrors[$fieldName]) && $formErrors[$fieldName]) {
                    $deliveryType->setFieldError($fieldName, true);
                } else {
                    $deliveryType->setFieldError($fieldName, false);
                }
            }
        }
        $translationsManager = $this->getService('translationsManager');

        if (!$this->validated) {
            $structureElement->errorMessage = $translationsManager->getTranslationByName('shoppingbasket.form_error');
        } elseif ($structureElement->isLastStep() && $formData['conditions'] != '1') {
            $structureElement->setFormError('conditions');
            $structureElement->errorMessage = $translationsManager->getTranslationByName('shoppingbasket.conditions_error');
            $this->validated = false;
        }

        if ($this->validated) {
            if ($structureElement->receiverIsPayer && $customFields = $structureElement->getCustomFieldsList()) {
                foreach ($customFields as &$field) {
                    $fieldName = $field->fieldName;
                    if ($field->autocomplete == 'company' && $structureElement->$fieldName != '') {
                        $structureElement->payerCompany = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'fullName' && $structureElement->$fieldName != '') {
                        $values = explode(' ', trim($structureElement->$fieldName));
                        $structureElement->payerFirstName = $values[0];
                        if (isset($values[1])) {
                            $structureElement->payerLastName = $values[1];
                        }
                    } elseif ($field->autocomplete == 'firstName' && $structureElement->$fieldName != '') {
                        $structureElement->payerFirstName = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'lastName' && $structureElement->$fieldName != '') {
                        $structureElement->payerLastName = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'email' && $structureElement->$fieldName != '') {
                        $structureElement->payerEmail = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'phone' && $structureElement->$fieldName != '') {
                        $structureElement->payerPhone = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'address' && $structureElement->$fieldName != '') {
                        $structureElement->payerAddress = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'city' && $structureElement->$fieldName != '') {
                        $structureElement->payerCity = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'country' && $structureElement->$fieldName != '') {
                        $structureElement->payerCountry = $structureElement->$fieldName;
                    } elseif ($field->autocomplete == 'postIndex' && $structureElement->$fieldName != '') {
                        $structureElement->payerPostIndex = $structureElement->$fieldName;
                    }
                }
            }
            $visitorsManager = $this->getService(VisitorsManager::class);
            $visitorData = [];
            if ($structureElement->payerFirstName) {
                $visitorData['firstName'] = $structureElement->payerFirstName;
            }
            if ($structureElement->payerLastName) {
                $visitorData['lastName'] = $structureElement->payerLastName;
            }
            if ($structureElement->payerEmail) {
                $visitorData['email'] = $structureElement->payerEmail;
            }
            if ($structureElement->payerPhone) {
                $visitorData['phone'] = $structureElement->payerPhone;
            }
            $visitorsManager->updateCurrentVisitorData($visitorData);

            $structureElement->saveShoppingBasketForm();

            if($structureElement->isLastStep()) {
                if($structureElement->paymentMethodId) {
                    $controller->redirect($structureElement->URL . 'id:' . $structureElement->id
                        . '/action:pay/bank:' . $structureElement->paymentMethodId . '/');
                }else {
                    $structureElement->setViewName('selection');
                }
            }else {
                $nextStep = $structureElement->getNextStep();
                $controller->redirect($structureElement->URL . 'step:' . $nextStep->structureName . '/');
            }
        } else {
            $structureElement->executeAction('show');
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'payerCompany',
            'payerFirstName',
            'payerLastName',
            'payerEmail',
            'payerPhone',
            'payerAddress',
            'payerCity',
            'payerPostIndex',
            'payerCountry',
            'receiverIsPayer',
            'subscribe',
            'conditions',
            'paymentMethodId',
        ];
        $expectedFields = array_merge($expectedFields, $this->structureElement->getCustomExpectedFields());
    }

    public function setValidators(&$validators)
    {
        foreach($this->structureElement->getCurrentStepElements() as $stepContentElement) {
            if($stepContentElement instanceof shoppingBasketStepPaymentsElement) {
                $validators['paymentMethodId'][] = 'notEmpty';
            }

            if($stepContentElement instanceof shoppingBasketStepDeliveryElement) {
                $receiverIsPayer = true;
                if (!isset($this->elementFormData['receiverIsPayer']) || $this->elementFormData['receiverIsPayer'] != '1') {
                    $receiverIsPayer = false;
                }
                if (!$receiverIsPayer) {
                    $validators['payerFirstName'][] = 'notEmpty';
                    $validators['payerLastName'][] = 'notEmpty';
                    $validators['payerPhone'][] = 'notEmpty';
                    $validators['payerEmail'][] = 'email';
                }

                $validators = $validators + $this->structureElement->getCustomValidators();
            }
        }
    }

    public function getExtraModuleFields()
    {
        return $this->structureElement->getCustomModuleFields();
    }
}