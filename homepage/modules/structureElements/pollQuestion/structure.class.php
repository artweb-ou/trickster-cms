<?php

class pollQuestionElement extends structureElement
{
    public $dataResourceName = 'module_poll_question';
    protected $allowedTypes = ['pollAnswer'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $answersList;
    protected $formFieldsList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['questionText'] = 'text';
        $moduleStructure['multiChoice'] = 'checkbox';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'questionText';
    }

    public function getAnswersList()
    {
        if (is_null($this->answersList)) {
            $structureManager = $this->getService('structureManager');
            $this->answersList = $structureManager->getElementsChildren($this->id);
        }
        return $this->answersList;
    }
}

