<?php

class pollAnswerElement extends structureElement
{
    public $dataResourceName = 'module_poll_answer';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $formFieldsList;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['answerText'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'answerText';
    }
}

