<?php

class showRoot extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $languagesList = [];
            $languageNames = [];
            if ($childrenList = $structureElement->getChildrenList()) {
                foreach ($childrenList as &$element) {
                    if ($element->structureType == 'language') {
                        if (!$element->hidden) {
                            $languagesList[] = $element;
                        }
                        $languageNames[$element->id] = $element->title;
                    }
                }
            }

            $renderer = renderer::getInstance();
            $renderer->assign('languagesList', $languagesList);
            $renderer->assign('rootElement', $structureElement);
            $renderer->assign('languageNames', $languageNames);
        }
    }
}