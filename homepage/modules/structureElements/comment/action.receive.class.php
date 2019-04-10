<?php

class receiveComment extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param commentElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->persistElementData();
            if ($targetElement = $structureElement->getTarget()) {
                $structureElement->targetType = $targetElement->structureType;
            }
        }

        $structureElement->setViewName('form');
        $controller->redirect($structureElement->URL);
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'author',
            'userId',
            'email',
            'content',
            'ipAddress',
            'approved',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['content'][] = 'notEmpty';
    }
}