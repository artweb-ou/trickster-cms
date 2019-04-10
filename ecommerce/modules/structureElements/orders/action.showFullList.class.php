<?php

class showFullListOrders extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        //		$structureElement->setViewName('lisst');
        if ($structureElement->final) {
            $collection = persistableCollection::getInstance('structure_elements');

            $conditions = [];
            $conditions[] = [
                'column' => 'structureType',
                'action' => '=',
                'argument' => 'order',
            ];

            $order = ['dateCreated' => 'asc'];
            $limit = 1;
            $startStamp = false;
            if ($records = $collection->conditionalLoad('dateCreated', $conditions, $order, $limit)) {
                $firstRecord = reset($records);
                $startStamp = $firstRecord['dateCreated'];
            }

            $order = ['dateCreated' => 'desc'];
            $limit = 1;
            $endStamp = false;
            if ($records = $collection->conditionalLoad('dateCreated', $conditions, $order, $limit)) {
                $firstRecord = reset($records);
                $endStamp = $firstRecord['dateCreated'];
            }

            $filterSelector = [];
            if ($startStamp !== false && $endStamp !== false) {
                $startMonth = date('n', $startStamp);
                $startYear = date('Y', $startStamp);

                $endMonth = date('n', $endStamp);
                $endYear = date('Y', $endStamp);

                for ($currentYear = $startYear; $currentYear <= $endYear; $currentYear++) {
                    if ($currentYear == $startYear) {
                        $currentMonth = $startMonth;
                    } else {
                        $currentMonth = 1;
                    }

                    do {
                        $currentStartDate = '01.' . sprintf('%02d', $currentMonth) . '.' . $currentYear;
                        $currentEndDate = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear) . '.' . sprintf('%02d', $currentMonth) . '.' . $currentYear;

                        // check if this month has any orders
                        $currentStartDateTimeStamp = strtotime($currentStartDate);
                        $currentEndDateTimeStamp = strtotime($currentEndDate);

                        $conditions = [];
                        $conditions[] = [
                            'column' => 'dateCreated',
                            'action' => '>=',
                            'argument' => $currentStartDateTimeStamp,
                        ];
                        $conditions[] = [
                            'column' => 'dateCreated',
                            'action' => '<=',
                            'argument' => $currentEndDateTimeStamp,
                        ];
                        $conditions[] = [
                            'column' => 'structureType',
                            'action' => '=',
                            'argument' => 'order',
                        ];
                        if ($records = $collection->conditionalLoad('id', $conditions, [], 1)) {
                            $filterSelector[] = [
                                'name' => sprintf('%02d', $currentMonth) . '.' . $currentYear,
                                'value' => $currentStartDate . '-' . $currentEndDate,
                            ];
                        }
                        $currentMonth++;
                    } while ($currentYear < $endYear && $currentMonth <= 12 || $currentYear == $endYear && $currentMonth <= $endMonth);
                }
            }
            $structureElement->filterSelector = $filterSelector;
            $structureElement->prepareSettingsData();

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'orders.list.tpl');
            }
        }
    }
}

if (!function_exists('cal_days_in_month')) {
    function cal_days_in_month($calenderType, $currentMonth, $currentYear)
    {
        return date("d", strtotime('01.' . $currentMonth . '.' . $currentYear . ' +1 months -1 days'));
    }
}