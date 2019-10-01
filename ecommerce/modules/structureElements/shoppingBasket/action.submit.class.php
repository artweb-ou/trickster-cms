<?php

/**
 * Class submitShoppingBasket
 *
 * @property shoppingBasketElement $structureElement
 */
class submitShoppingBasket extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param shoppingBasketElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        /**
         * @var shoppingBasket $shoppingBasket
         */
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;
        if (!$structureElement->shoppingBasket->getProductsList()) {
            // session expired
            $controller->redirect($structureElement->URL);
        }
        $renderer = $this->getService('renderer');
        $renderer->assign('shoppingBasket', $structureElement);
        $currentStep = $structureElement->getCurrentStepElement();
        $formErrors = $structureElement->getFormErrors();
        $formData = $structureElement->getFormData();
        if ($this->stepContentIsUsingCustomFields()) {
            $deliveryType = $shoppingBasket->getSelectedDeliveryType();
            if ($customFields = $structureElement->getCustomFieldsList()) {
                foreach ($customFields as $field) {
                    $fieldName = $field->fieldName;
                    $deliveryType->setFieldValue($fieldName, $formData[$fieldName]);
                    if (isset($formErrors[$fieldName]) && $formErrors[$fieldName]) {
                        $deliveryType->setFieldError($fieldName, true);
                    } else {
                        $deliveryType->setFieldError($fieldName, false);
                    }
                }
            }
        }
        $translationsManager = $this->getService('translationsManager');

        if (!$this->validated) {
            $structureElement->errorMessage = $translationsManager->getTranslationByName('shoppingbasket.form_error');
        } elseif ($currentStep->getStepElementByType('agreement') && $formData['conditions'] != '1') {
            $structureElement->setFormError('conditions');
            $structureElement->errorMessage = $translationsManager->getTranslationByName('shoppingbasket.conditions_error');
            $this->validated = false;
        }

        if ($this->validated) {
            if ($this->stepContentIsUsingCustomFields()) {
                if ($structureElement->receiverIsPayer && $customFields = $structureElement->getCustomFieldsList()) {
                    foreach ($customFields as $field) {
                        $fieldName = $field->fieldName;
                        if ($structureElement->$fieldName != '') {
                            if ($field->autocomplete == 'company') {
                                $structureElement->payerCompany = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'fullName') {
                                $values = explode(' ', trim($structureElement->$fieldName));
                                $structureElement->payerFirstName = $values[0];
                                if (isset($values[1])) {
                                    $structureElement->payerLastName = $values[1];
                                }
                            } elseif ($field->autocomplete == 'firstName') {
                                $structureElement->payerFirstName = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'lastName') {
                                $structureElement->payerLastName = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'email') {
                                $structureElement->payerEmail = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'phone') {
                                $structureElement->payerPhone = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'address') {
                                $structureElement->payerAddress = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'city') {
                                $structureElement->payerCity = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'country') {
                                $structureElement->payerCountry = $structureElement->$fieldName;
                            } elseif ($field->autocomplete == 'postIndex') {
                                $structureElement->payerPostIndex = $structureElement->$fieldName;
                            }
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
            }
            //payment method id is sent from form, update it.
            if ($structureElement->paymentMethodId) {
                $shoppingBasket->updateBasketFormData([
                    'paymentMethodId' => $structureElement->paymentMethodId,
                ]);
            }
            if ($structureElement->isLastStep()) {
                //last step was submitted, try to pay
                $structureElement->executeAction('pay');
            } else {
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
        foreach ($this->structureElement->getCurrentStepElements() as $stepContentElement) {
            $validators = $validators + $stepContentElement->getValidators($this->elementFormData);
        }

        if ($this->stepContentIsUsingCustomFields()) {
            $validators = $validators + $this->structureElement->getCustomValidators();
        }
    }

    public function stepContentIsUsingCustomFields()
    {
        foreach ($this->structureElement->getCurrentStepElements() as $stepContentElement) {
            if ($stepContentElement->useCustomFields()) {
                return true;
            }
        }

        return false;
    }


    public function getExtraModuleFields()
    {
        return $this->structureElement->getCustomModuleFields();
    }
}