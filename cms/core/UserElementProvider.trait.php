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
                /**
                 * @var structureManager $structureManager
                 */
                $structureManager = $this->getService('structureManager');
                $this->userElement = $structureManager->getElementById($userId, null, true);
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