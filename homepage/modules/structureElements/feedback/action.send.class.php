<?php

class sendFeedback extends structureElementAction
{
    use AjaxFormTrait;
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $translationsManager = $this->getService('translationsManager');

        if ($this->validated && $this->validateAjaxRequest()) {
            $settings = $this->getService('settingsManager')->getSettingsList();
            $subject = $structureElement->title;

            $data = [
                'groups' => [],
                'heading' => $subject,
            ];

            $emailToCheck = false;

            $fullName = false;
            $company = false;
            $firstName = false;
            $lastName = false;
            $fromName = isset($settings['default_sender_name']) ? $settings['default_sender_name'] : 'noreply';
            $fromEmail = isset($settings['default_sender_email']) ? $settings['default_sender_email'] : 'noreply@noreply.com';

            if (!$structureElement->destination) {
                $receiverEmail = isset($settings['default_sender_email']) ? $settings['default_sender_email'] : false;
            } else {
                $receiverEmail = $structureElement->destination;
            }

            $files = [];

            $answerElementTitle = '';
            $answerFieldValues = [];
            $answerFiles = [];

            foreach ($structureElement->getCustomFieldsGroups() as $groupElement) {
                $groupInfo = [
                    'title' => $groupElement->title,
                    'formFields' => [],
                ];
                foreach ($groupElement->getFormFields() as $formField) {
                    if ($formField->fieldType == 'fileinput') {
                        if ($dataChunk = $structureElement->getDataChunk($formField->fieldName)) {
                            if (!empty($dataChunk->originalName)) {
                                $storageName = $structureElement->id . '_' . $formField->id . '_' . time();
                                $answerFiles[$formField->id] = [
                                    'originalName' => $dataChunk->originalName,
                                    'storageName' => $storageName,
                                ];
                                if ($dataChunk instanceof ElementStorageValueHolderInterface) {
                                    $dataChunk->setStorageValue($storageName);
                                }
                                if ($dataChunk instanceof ExtraDataHolderDataChunkInterface) {
                                    $dataChunk->persistExtraData();
                                }
                            }
                            $configurationManager = controller::getInstance()->getConfigManager();
                            $uploadsPath = $configurationManager->get('paths.uploads');
                            $files[] = [

                                'fullName' => $uploadsPath . $dataChunk->getStorageValue(),
                                'originalName' => $dataChunk->originalName,
                            ];
                        }
                    } else {
                        $fieldName = $formField->fieldName;
                        $value = $structureElement->$fieldName;
                        if ($formField->fieldType == 'dateInput') {
                            // value from this input may be in YYYY-MM-DD format
                            // see also: feedbackElement::getCustomFieldsList workaround
                            $parts = explode('-', $value);
                            if (count($parts) === 3) {
                                $value = implode('.', array_reverse($parts));
                            }
                        }
                        $fieldInfo = [
                            'fieldName' => $fieldName,
                            'fieldTitle' => $formField->title,
                            'fieldType' => $formField->fieldType,
                            'fieldValue' => $value,
                        ];
                        $groupInfo['formFields'][] = $fieldInfo;

                        $answerFieldValues[$formField->id] = $value;

                        if ($formField->autocomplete == 'fullName') {
                            $fullName = $value;
                        } elseif ($formField->autocomplete == 'company') {
                            $company = $value;
                        } elseif ($formField->autocomplete == 'firstName') {
                            $firstName = $value;
                        } elseif ($formField->autocomplete == 'lastName') {
                            $lastName = $value;
                        } elseif ($formField->autocomplete == 'phone') {
                            $phone = $value;
                        } elseif ($formField->autocomplete == 'email') {
                            $emailToCheck = $value;
                            $fromEmail = $value;
                        }
                    }
                }
                $data['groups'][] = $groupInfo;
            }
            $visitorManager = $this->getService('VisitorsManager');
            if ($visitor = $visitorManager->getCurrentVisitor()) {
                if ($firstName && $lastName) {
                    $visitor->firstName = $firstName;
                    $visitor->lastName = $lastName;
                    $fromName = $firstName . ' ' . $lastName;
                } elseif ($fullName) {
                    $visitor->firstName = $fullName;
                    $fromName = $fullName;
                } elseif ($company) {
                    $fromName = $company;
                }
                $visitor->email = $fromEmail;
                $visitor->phone = $phone;
                $visitorManager->updateVisitor($visitor);
            }
            $spamChecker = $this->getService('SpamChecker');
            if ($emailToCheck && !$spamChecker->checkEmail($emailToCheck)) {
                $structureElement->errorMessage = $translationsManager->getTranslationByName('feedback.emailsendingfailed');
            } else {
                $emailDispatcher = $this->getService('EmailDispatcher');
                $newDispatchment = $emailDispatcher->getEmptyDispatchment();
                $newDispatchment->setFromName($fromName);
                $newDispatchment->setFromEmail($fromEmail);
                $newDispatchment->setSubject($subject);
                $newDispatchment->setData($data);
                if ($files) {
                    foreach ($files as $file) {
                        $newDispatchment->registerAttachment($file['fullName'], $file['originalName']);
                    }
                }
                $newDispatchment->setReferenceId($structureElement->id);
                $newDispatchment->setType('feedback');
                $newDispatchment->registerReceiver($receiverEmail, null);

                $answerElement = $structureManager->createElement('feedbackAnswer', 'show', $structureElement->id, false, 'feedbackAnswer');

                if ($answerElement) {
                    $answerElement->prepareActualData();
                    $answerElement->title = $answerElementTitle;
                    $answerElement->persistElementData();
                    if ($visitor) {
                        $event = new Event();
                        $event->setType('feedback');
                        $event->setVisitorId($visitor->id);
                        $event->setElementId($answerElement->id);
                        $eventLogger = $this->getService('eventsLog');
                        $eventLogger->saveEvent($event);
                    }
                    foreach ($answerFieldValues as $fieldId => $fieldValue) {
                        if (is_array($fieldValue)) {
                            foreach ($fieldValue as $value) {
                                $answerElement->addGenericValue($fieldId, $value);
                            }
                        } else {
                            $answerElement->addGenericValue($fieldId, $fieldValue);
                        }
                    }
                    foreach ($answerFiles as $fieldId => $fileInfo) {
                        $answerElement->addFile($fieldId, $fileInfo);
                    }
                }

                if ($emailDispatcher->startDispatchment($newDispatchment)) {
                    $structureElement->resultMessage = $translationsManager->getTranslationByName('feedback.emailsendingsuccess');
                    $this->ajaxFormSuccess = true;
                } else {
                    $structureElement->errorMessage = $translationsManager->getTranslationByName('feedback.emailsendingfailed');
                }
            }
        }

        $this->sendAjaxFormResponse($structureElement);
        //        $structureElement->setViewName('form');
    }

    public function getExtraModuleFields()
    {
        return $this->structureElement->getCustomModuleFields();
    }

    public function setValidators(
        &$validators
    ) {
        $validators = $this->structureElement->getCustomValidators();
    }

    public function setExpectedFields(
        &$expectedFields
    ) {
        $expectedFields = array_merge($expectedFields, $this->structureElement->getCustomExpectedFields());
    }
}