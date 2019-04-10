<?php

class NewsMailSubscriptionServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new NewsMailSubscription();
    }

    public function makeInjections($instance)
    {
        $newsMailSubscription = $instance;
        if ($structureManager = $this->registry->getService('structureManager')) {
            $newsMailSubscription->setStructureManager($structureManager);
        }

        if ($db = $this->registry->getService('db')) {
            $newsMailSubscription->setDatabase($db);
        }

        if ($linksManager = $this->registry->getService('linksManager')) {
            $newsMailSubscription->setLinksManager($linksManager);
        }

        if ($spamChecker = $this->registry->getService('SpamChecker')) {
            $newsMailSubscription->setSpamChecker($spamChecker);
        }

        return $newsMailSubscription;
    }
}