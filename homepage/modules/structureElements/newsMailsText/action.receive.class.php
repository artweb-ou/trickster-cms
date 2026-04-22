<?php

class receiveNewsMailsText extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param newsMailsTextElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if ($structureElement->title != '') {
                $structureElement->structureName = $structureElement->title;
            }

            $structureElement->file = $structureElement->id;
            $structureElement->originalName = $structureElement->getDataChunk("file")->originalName;

            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }

        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'from',
            'fromEmail',
            'title',
            'content',
            'file',
            'customTemplate',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['from'][] = 'notEmpty';
        $validators['title'][] = 'notEmpty';
    }
}