<?php

class publicReceiveComment extends structureElementAction
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
            if ($targetElement = $structureManager->getElementsFirstParent($structureElement->id)) {
                if ($targetElement instanceof CommentsHolderInterface) {
                    $user = $this->getService('user');
                    $structureElement->userId = $user->id;
                    if (!$structureElement->dateTime) {
                        $structureElement->dateTime = time();
                    }
                    if (!$structureElement->targetType) {
                        $structureElement->targetType = $targetElement->structureType;
                    }
                    $structureElement->persistElementData();
                    $structureElement->logCreation();
                    $this->getService('linksManager')
                        ->linkElements($targetElement->id, $structureElement->id, "commentTarget");

                    $targetElement->recalculateComments();

                    if ($commentsElementId = $structureManager->getElementIdByMarker('comments')) {
                        $structureManager->moveElement($targetElement->id, $commentsElementId, $structureElement->id);
                    }

                    $controller->redirect($structureElement->getInitialTarget()->getUrl());
                }
            }
        }
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