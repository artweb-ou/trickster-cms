<?php

class SocialErrorMessage
{
    public $messageCode;
    protected $messages = [
        '1' => 'error.wrong_user_is_logged_in',
        '2' => 'something_else',
    ];

    public function __construct($messageCode)
    {
        $this->messageCode = $messageCode;
    }

    public function getErrorText()
    {
        if(isset($this->messages[$this->messageCode])) {
            return $this->messages[$this->messageCode];
        }

        return false;
    }
}


