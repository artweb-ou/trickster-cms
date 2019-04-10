<?php

class sendDataUser extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated === true) {
            $structureElement->generatePassword();

            $data = [
                "company" => $structureElement->company,
                "firstName" => $structureElement->firstName,
                "lastName" => $structureElement->lastName,
                "email" => $structureElement->email,
                "phone" => $structureElement->phone,
                "address" => $structureElement->address,
                "city" => $structureElement->city,
                "postIndex" => $structureElement->postIndex,
                "country" => $structureElement->country,
                "userName" => $structureElement->userName,
                "password" => $structureElement->password,
                "website" => $structureElement->website,
            ];

            $translationsManager = $this->getService('translationsManager');
            $emailDispatcher = $this->getService('EmailDispatcher');
            $newDispatchment = $emailDispatcher->getEmptyDispatchment();
            $settings = $this->getService('settingsManager')->getSettingsList();
            $newDispatchment->setFromName($settings['default_sender_name'] ? $settings['default_sender_name'] : "");
            $newDispatchment->setFromEmail($settings['default_sender_email'] ? $settings['default_sender_email'] : "");
            $newDispatchment->setSubject($translationsManager->getTranslationByName("email.userdata_subject"));
            $newDispatchment->setData($data);
            $newDispatchment->setDataLifeTime(60);
            $newDispatchment->setReferenceId($structureElement->id);
            $newDispatchment->setType("userData");
            $newDispatchment->registerReceiver($structureElement->email, null);

            if ($emailDispatcher->startDispatchment($newDispatchment)) {
                $structureElement->persistElementData();
                $structureElement->resultMessage = $translationsManager->getTranslationByName('userdata.emailsendingsuccess');
            } else {
                $structureElement->errorMessage = $translationsManager->getTranslationByName('userdata.emailsendingfailed');
            }
        }
        $structureElement->executeAction("showForm");
    }

    public function setValidators(&$validators)
    {
    }

    public function setExpectedFields(&$expectedFields)
    {
    }
}

