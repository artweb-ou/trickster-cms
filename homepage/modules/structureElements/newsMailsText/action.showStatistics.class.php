<?php

class showStatisticsNewsMailsText extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            if ($structureElement->hasActualStructureInfo()) {

                $emailDispatcher = $this->getService('EmailDispatcher');
                $historyIndex = $emailDispatcher->getReferencedDispatchmentHistory($structureElement->id);
                $totalDispatchments = count($historyIndex);
                $dispatchmentsInfo = array(
                    'total'    => $totalDispatchments,
                    'fail'     => 0,
                    'awaiting' => 0,
                    'success'  => 0,
                );
                foreach ($historyIndex as $sentMail) {
                    $dispatchmentsInfo[$sentMail['status']]++;
                }
                $dispatchmentEventsInfo = array();
                $userClickedLinksCombined = array();
                $events = array();
                $structureId = $structureElement->id;
                $db = $this->getService('db');
                $types = [
                    'newsMail_emailOpened',
                    'newsMail_externalLinkClicked',
                    'newsMail_linkClicked',
                    'newsMail_unsubscribe',
                    'newsMail_unsubscribe_1step',
                    'newsMail_viewFromBrowser'
                ];
                $eventsType = $db->table('eventtype')->whereIn('type', $types)->get();
                $eventsList = [];
                foreach ($eventsType as $event) {
                    if (in_array($event['type'], $event)) {
                        $eventsList[] = $event['id'];
                    }
                }
                $records = $db->table('event')
                    ->select
                    (
                        'event.Id',
                        'event.typeId',
                        'event.visitorId',
                        'event.elementId',
                        'event.time',
                        'uri'
                    )
                    ->leftJoin($db->raw('(
                    SELECT 
                        `engine_link_event_uri`.`eventId`,	
                        `engine_visitor_uri`.`uri`
                    FROM
                        `engine_visitor_uri`,
                        `engine_link_event_uri`
                    WHERE
                         `engine_visitor_uri`.`id` = `engine_link_event_uri`.`uriId`
                    ) uri'), function ($join) {
                        $join->on('event.id', '=', $this->getService('db')
                            ->raw('uri.eventId'));
                    })
                    ->where('event.elementId', '=', $structureId)
                    ->whereIn('typeId', $eventsList)
                    ->get();
                if ($records) {
                    if (!empty($eventsType)) {
                        foreach ($eventsType as $type) {
                            if($type['type'] === 'newsMail_linkClicked') {
                                $newsMailLinkClickedId = $type['id'];
                            }
                            if ($type['type'] === 'newsMail_emailOpened') {
                                $newsMailEmailOpenedId = $type['id'];
                            }
                            if($type['type'] === 'newsMail_externalLinkClicked') {
                                $newsMailExternalLinkClickedId = $type['id'];
                            }
                            $events[$type['id']] = $type['type'];
                        }
                    }
                    foreach ($records as $record) {
                        if ($record['typeId'] != $newsMailLinkClickedId && $record['typeId'] != $newsMailExternalLinkClickedId) {
                            $dispatchmentEventsInfo[$events[$record['typeId']]]['users'][$record['visitorId']]++;
                            $dispatchmentEventsInfo[$events[$record['typeId']]]['clicks']++;
                        } else {
                            if ($record['uri'] != null) {
                                if (!array_key_exists($events[$record['typeId']], $dispatchmentEventsInfo)) {
                                    $dispatchmentEventsInfo[$events[$record['typeId']]]['users'] = [];
                                };
                                if (!array_key_exists($record['visitorId'], $dispatchmentEventsInfo[$events[$record['typeId']]]['users'])) {
                                    $dispatchmentEventsInfo[$events[$record['typeId']]]['users'][$record['visitorId']][$record['uri']];
                                };
                                $dispatchmentEventsInfo[$events[$record['typeId']]]['users'][$record['visitorId']]++;
                                $dispatchmentEventsInfo[$events[$record['typeId']]]['clicks']++;
                                $dispatchmentEventsInfo[$events[$record['typeId']]]['links'][$record['uri']]['users'][$record['visitorId']]++;
                                $dispatchmentEventsInfo[$events[$record['typeId']]]['links'][$record['uri']]['clicks']++;
                                $userClickedLinksCombined[$record['visitorId']]++;
                            }
                        }
                    }
                }
                $structureElement->dispatchmentsInfo = $dispatchmentsInfo;
                $structureElement->dispatchmentEventsInfo = $dispatchmentEventsInfo;
                $structureElement->userClickedLinksCombined = $userClickedLinksCombined;
            }
            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'newsMailsText.statistics.tpl');
            }
        }
    }
}