<?php

class showLinkListItem extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($fixedElement = $structureElement->getFixedElement()) {
            if ($structureElement->title == '') {
                $structureElement->title = $fixedElement->title;
            }
            if ($structureElement->link == '') {
                $structureElement->link = $fixedElement->URL;
            }
            if ($structureElement->originalName == '' && $fixedElement->originalName) {
                $structureElement->originalName = $fixedElement->originalName;
                $structureElement->image = $fixedElement->image;
            }
            if ($structureElement->content == '' && $fixedElement->introduction) {
                $structureElement->content = $fixedElement->introduction;
            }
            if ($structureElement->content == '' && $fixedElement->content) {
                $structureElement->content = $fixedElement->content;
            }
            if ($structureElement->linkText == '') {
                $translationsManager = $this->getService('translationsManager');
                if ($translations = $translationsManager->getTranslationByName('linklist.readmore')) {
                    $structureElement->linkText = $translations;
                }
            }
        }
    }
}