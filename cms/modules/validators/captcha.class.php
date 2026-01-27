<?php

use App\Users\CurrentUser;

class captchaValidator extends validator
{
    public function execute($formValue)
    {
        return $formValue && strtolower($formValue) == $this->getService(CurrentUser::class)->getStorageAttribute('last_captcha');
    }
}

