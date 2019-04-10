<?php

trait EventLoggingElementTrait
{
    public function logViewEvent()
    {
        $this->logVisitorEvent($this->structureType . '_view');
    }

    public function logVisitorEvent($type, array $parameters = [])
    {
        $visitorsManger = $this->getService('VisitorsManager');
        $visitor = $visitorsManger->getCurrentVisitor();
        if (!$visitor) {
            return;
        }
        $eventsLog = $this->getService('eventsLog');
        $event = $eventsLog->generateEvent($type, $this->id, $parameters);
        $eventsLog->saveEvent($event);
    }
}