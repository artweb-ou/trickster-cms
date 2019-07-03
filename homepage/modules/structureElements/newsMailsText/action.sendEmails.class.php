<?php

class sendEmailsNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setViewName('form');
        if ($structureElement->hasActualStructureInfo()) {
            $structureManager = $this->getService('structureManager');
            $linksManager = $this->getService('linksManager');
            $selectedAddressesIds = $structureElement->selectedEmails;
            if ($structureElement->selectedGroupsIds) {
                foreach ($structureElement->selectedGroupsIds as $groupId) {
                    if ($groupAddressesIds = $linksManager->getConnectedIdList($groupId, 'newsmailGroup', 'parent')) {
                        $selectedAddressesIds = array_merge($selectedAddressesIds, $groupAddressesIds);
                    }
                }
            }
            if ($selectedAddressesIds) {
                $addressesElement = $structureManager->getElementByMarker('newsMailsAddresses');
                if ($addresses = $structureManager->getElementsByIdList($selectedAddressesIds, $addressesElement->id)) {
                    $emailDispatcher = $this->getService('EmailDispatcher');
                    $newDispatchment = $emailDispatcher->getEmptyDispatchment();
                    $settings = $this->getService('settingsManager')->getSettingsList();
                    $senderName = $structureElement->from ? $structureElement->from : (isset($settings['default_sender_name']) ? $settings['default_sender_name'] : "");
                    $senderEmail = $structureElement->fromEmail ? $structureElement->fromEmail : (isset($settings['default_sender_email']) ? $settings['default_sender_email'] : "");
                    $newDispatchment->setFromName($senderName);
                    $newDispatchment->setFromEmail($senderEmail);
                    $newDispatchment->setSubject($structureElement->title);
                    $newDispatchment->setData($structureElement->getDispatchmentData());
                    $newDispatchment->setReferenceId($structureElement->id);
                    $newDispatchment->setType($structureElement->getDispatchmentType());

                    foreach ($addresses as &$addressElement) {
                        $newDispatchment->registerReceiver($addressElement->email, null, $addressElement->id);
                    }
                    $emailDispatcher->appointDispatchment($newDispatchment);
                }
            }
        }
        $structureElement->executeAction('showForm');
        $controller->redirect($structureElement->URL);
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'selectedEmails',
            'selectedGroupsIds',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

