<?php

class captchaValidator extends validator
{
    public function execute($formValue)
    {
        return $formValue && strtolower($formValue) == $this->getService('user')->getStorageAttribute('last_captcha');
    }
}

