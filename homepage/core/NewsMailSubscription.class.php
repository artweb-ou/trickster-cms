<?php

class NewsMailSubscription
{
    protected $database;
    /**
     * @var SpamChecker
     */
    protected $spamChecker;
    /**
     * @var structureManager
     */
    protected $structureManager;
    /**
     * @var linksManager
     */
    protected $linksManager;
    protected $mailsElement;
    protected $subscriberElementsIndex = [];

    public function setDatabase($database)
    {
        $this->database = $database;
    }

    public function setStructureManager($structureManager)
    {
        $this->structureManager = $structureManager;
    }

    public function setLinksManager($linksManager)
    {
        $this->linksManager = $linksManager;
    }

    /**
     * @param SpamChecker $spamChecker
     */
    public function setSpamChecker($spamChecker)
    {
        $this->spamChecker = $spamChecker;
    }

    /**
     * @param $email
     * @param string $groupMarker
     * @param bool $groupId
     * @param bool $name
     * @return bool
     */
    public function subscribeEmailToNewsMailGroup(
        $email,
        $groupMarker = 'newsmail_subscribed',
        $groupId = false,
        $name = false
    ) {
        $subscribed = false;
        $subscriberElement = $this->getSubscriberElementByEmail($email);
        if (!$subscriberElement) {
            $subscriberElement = $this->createSubscriberElement($email, $name);
        }
        if ($subscriberElement) {
            if ($groupId || $groupId = $this->structureManager->getElementIdByMarker($groupMarker)) {
                $this->linksManager->linkElements($groupId, $subscriberElement->id, 'newsmailGroup');
                $subscribed = true;
            }
        }
        return $subscribed;
    }

    public function getSubscriberElementByEmail($email)
    {
        if (!isset($this->subscriberElementsIndex[$email])) {
            $this->subscriberElementsIndex[$email] = false;
            $db = $this->database;
            $id = $db->table('module_newsmailaddress')
                ->select('id')
                ->where('email', '=', $email)
                ->limit(1)
                ->value('id');
            if ($id) {
                //if we are not in admin panel, preload mails element
                if ($this->loadMailsElement()) {
                    $this->subscriberElementsIndex[$email] = $this->structureManager->getElementById($id);
                }
            }
        }
        return $this->subscriberElementsIndex[$email];
    }

    protected function loadMailsElement()
    {
        if ($this->mailsElement === null) {
            if ($mailsElementId = $this->structureManager->getElementIdByMarker('newsMailsAddresses')) {
                if ($this->mailsElement = $this->structureManager->getElementById($mailsElementId, null, true)) {
                    return $this->mailsElement;
                }
            }
        }
        return $this->mailsElement;
    }

    public function createSubscriberElement($email, $name = '')
    {
        $email = trim($email);
        if (!$this->spamChecker->checkEmail($email)) {
            return false;
        }
        $subscriberElement = false;
        if ($mailsElement = $this->loadMailsElement()) {
            if ($subscriberElement = $this->structureManager->createElement('newsMailAddress', 'showForm', $mailsElement->id)) {
                $subscriberElement->prepareActualData();
                $subscriberElement->structureName = $email;
                if (!$name) {
                    $newData = [
                        'personalName' => $email,
                        'email' => $email,
                    ];
                } else {
                    $newData = [
                        'personalName' => $name,
                        'email' => $email,
                    ];
                }

                if ($subscriberElement->importExternalData($newData)) {
                    $subscriberElement->persistElementData();
                }
            }
        }
        $this->subscriberElementsIndex[$email] = $subscriberElement;
        return $subscriberElement;
    }

    /**
     * @param $groupName
     * @param string $groupMarker
     * @return bool
     */
    public function checkGroup($groupMarker, $groupName)
    {
        $structureManager = $this->structureManager;
        $linksManager = $this->linksManager;
        $result = false;

        if (!$structureManager->getElementIdByMarker($groupMarker)) {
            if ($groupsId = $structureManager->getElementIdByMarker('newsMailsGroups')) {
                /*
                 * @var newsMailsGroupElement $newGroup
                 */
                if ($newGroup = $structureManager->createElement('newsMailsGroup', 'showForm')) {
                    $newGroup->title = $groupName;
                    $newGroup->structureName = $groupName;
                    $newGroup->marker = $groupMarker;
                    $newGroup->persistElementData();

                    $linksManager->unLinkElements($structureManager->getRootElementId(), $newGroup->id);
                    $linksManager->linkElements($groupsId, $newGroup->id, 'structure');
                    $result = $newGroup->id;
                }
            }
        } else {
            $result = true;
        }
        return $result;
    }
}
