<?php

use App\Users\CurrentUser;

class subscribeNewsMailForm extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param newsMailFormElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $result = false;
        if ($this->validated) {
            //force javascript check
            if (($check = (int)$controller->getParameter('check')) && (time() - $check < 60)) {
                /**
                 * @var NewsMailSubscription $newsMailSubscription
                 */
                if ($newsMailSubscription = $this->getService('NewsMailSubscription')) {
                    if ($newsMailSubscription->subscribeEmailToNewsMailGroup($structureElement->email)) {
                        $user = $this->getService(CurrentUser::class);
                        $user->setStorageAttribute('subscribed', true);

                        $result = true;
                        $structureElement->setSubscriptionStatus('success');
                        $structureElement->logVisitorEvent('newsletter_subscription');
                        $visitorsManager = $this->getService(VisitorsManager::class);
                        $visitorsManager->updateCurrentVisitorData(['email' => $structureElement->email]);
                    }
                }
            }
        }
        if (!$result) {
            $structureElement->setSubscriptionStatus('fail');
        }

        $renderer = $this->getService('renderer');
        if ($renderer instanceof rendererPluginAppendInterface) {
            $renderer->assignResponseData($structureElement->structureType, $structureElement->getElementData());
        }
    }

    public function setValidators(&$validators)
    {
        $validators['email'][] = 'notEmpty';
        $validators['email'][] = 'email';
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = ['email'];
    }
}