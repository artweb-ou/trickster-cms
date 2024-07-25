<?php

class SpamCheckerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new SpamChecker();
    }

    public function makeInjections($instance)
    {
        $spamChecker = $instance;
        $this->injectService($spamChecker, 'db');
        $this->injectService($spamChecker, 'VerifyMailAdapter');
        $this->injectService($spamChecker, 'VerifaliaAdapter');

        return $spamChecker;
    }
}