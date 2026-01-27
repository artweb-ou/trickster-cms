<?php

class captchaValidator extends validator
{
    public function execute($formValue)
    {
        return $formValue && strtolower($formValue) == $this->getService(user::class)->getStorageAttribute('last_captcha');
    }
}

