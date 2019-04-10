<?php

class openingHoursExceptionElement extends structureElement
{
    public $dataResourceName = 'module_openinghours_exception';
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $rooms;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['startDate'] = 'text';
        $moduleStructure['endDate'] = 'text';
        $moduleStructure['startTime'] = 'text';
        $moduleStructure['endTime'] = 'text';
        $moduleStructure['closed'] = 'checkbox';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields = [
            'title',
        ];
    }
}

