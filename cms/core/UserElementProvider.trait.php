<?php

trait UserElementProviderTrait
{
    private $userElement;

    /**
     * @return userElement
     */
    public function getUserElement()
    {
        if ($this->userElement === null) {
            $this->userElement = false;
            if ($userId = $this->getUserId()) {
                $structureManager = $this->getService('structureManager');
                if ($users = $structureManager->getElementsByIdList($userId)) {
                    $this->userElement = reset($users);
                }
            }
        }
        return $this->userElement;
    }

    /**
     * @return userElement
     * @deprecated
     */
    public function getUser()
    {
        return $this->getUserElement();
    }

    abstract public function getUserId();
}