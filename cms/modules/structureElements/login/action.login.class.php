<?php

class loginLogin extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $validated = false;
        $userName = $structureElement->userName;
        if ($this->validated === true && $userName != "crontab") {
            $user = $this->getService('user');
            $password = $structureElement->getFormValue('password');
            if ($userId = $user->checkUser($userName, $password)) {
                $validated = true;
                $structureElement->setViewName('result');
                $user->switchUser($userId);
                if ($controller->getApplicationName() != "admin") {
                    if ($structureElement->remember == '1') {
                        $this->getService('user')->rememberUser($userName, $userId);
                    } else {
                        $user->forgetUser(); // remove 'remember' cookie from possible previous login
                    }
                    $visitorsManager = $this->getService(VisitorsManager::class);
                    $visitorsManager->updateCurrentVisitorData(['email' => $user->email, 'userId' => $user->id]);
                }
                $redirectURL = $controller->fullURL;
                $controller->redirect($redirectURL);
            } else {
                $structureElement->setFormError('password', true);
            }
        }

        if (!$validated) {
            $structureElement->errorMessage = $this->getService('translationsManager')
                ->getTranslationByName('login.wrong_credentials', 'public_translations');
            $applicationName = $controller->getApplicationName();
            if ($applicationName == 'admin') {
                $structureElement->executeAction('showForm');
            } else {
                $structureElement->executeAction('show');
            }
        }
    }

    public function setValidators(&$validators)
    {
        $validators['userName'][] = 'notEmpty';
        $validators['password'][] = 'notEmpty';
        $validators['password'][] = 'password';
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'userName',
            'password',
            'remember',
        ];
    }
}