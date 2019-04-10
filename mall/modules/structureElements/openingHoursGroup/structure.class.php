<?php

class openingHoursGroupElement extends structureElement
{
    public $dataResourceName = 'module_openinghours_group';
    protected $allowedTypes = ['openingHoursException'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $rooms;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hoursData'] = 'jsonSerialized';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields = [
            'title',
        ];
    }

    public function getExceptions()
    {
        return $this->getChildrenList();
    }

    public function getOpeningHoursInfo()
    {
        // TODO: make a new class/trait. this method is duplicated in shop and openingHoursGroup
        $result = [];
        $daysHours = $this->hoursData;
        if ($daysHours && count($daysHours) == 7) {
            $translationsManager = $this->getService('translationsManager');
            for ($i = 0; $i < 7; ++$i) {
                $dayInfo = $daysHours[$i];
                $periodName = $translationsManager->getTranslationByName('calendar.weekday_abbreviation_'
                    . ($i + 1), null, true);
                $dayClosed = !empty($dayInfo['closed']) || (!$dayInfo['start'] && !$dayInfo['end']);
                if ($dayClosed) {
                    $periodTimes = $translationsManager->getTranslationByName('openinghoursinfo.closed', null, true);
                } else {
                    $periodTimes = $dayInfo['start'] . '-' . $dayInfo['end'];
                }
                $endDayNumber = $i;
                for ($j = $i + 1; $j < 7; ++$j) {
                    $nextDayInfo = $daysHours[$j];
                    $nextDayClosed = !empty($nextDayInfo['closed']) || (!$nextDayInfo['start'] && $nextDayInfo['end']);
                    if ($nextDayClosed && $dayClosed || ($nextDayClosed == $dayClosed && $nextDayInfo['start'] == $dayInfo['start']
                            && $nextDayInfo['end'] == $dayInfo['end'])
                    ) {
                        $endDayNumber = $j;
                    } else {
                        break;
                    }
                }
                if ($endDayNumber != $i) {
                    $periodName .= '-' . $translationsManager->getTranslationByName('calendar.weekday_abbreviation_'
                            . ($endDayNumber + 1), null, true);
                    $i = $endDayNumber;
                }
                $result[] = [
                    'name' => $periodName,
                    'times' => $periodTimes,
                ];
            }
        }
        return $result;
    }
}


