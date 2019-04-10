<?php

class newsMailFormElement extends menuDependantStructureElement
{
    use EventLoggingElementTrait;
    public $dataResourceName = 'module_newsmailform';
    public $defaultActionName = 'show';
    protected $allowedTypes = [];
    public $role = 'content';
    protected $message = '';
    protected $subscriptionStatus = false;

    /**
     * @param boolean $subscriptionStatus
     */
    public function setSubscriptionStatus($subscriptionStatus)
    {
        $this->subscriptionStatus = $subscriptionStatus;
        $translationsManager = $this->getService('translationsManager');
        if ($subscriptionStatus == 'success') {
            $this->message = $translationsManager->getTranslationByName('subscribe.thanksforsubscribing');
        } elseif ($subscriptionStatus == 'fail') {
            $this->message = $translationsManager->getTranslationByName('subscribe.subscribingfailed');
        } else {
            $this->message = '';
        }
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['email'] = 'email';
        $moduleStructure['description'] = 'html';
    }

    public function getElementData()
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'subscriptionStatus' => $this->subscriptionStatus,
        ];
    }
}