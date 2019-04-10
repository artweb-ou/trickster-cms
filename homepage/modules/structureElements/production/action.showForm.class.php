<?php

class showFormProduction extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $linksManager = $this->getService('linksManager');
            $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
            $publicRoot = $structureManager->getElementByMarker($marker);
            $languages = $structureManager->getElementsChildren($publicRoot->id);
            $structureElement->galleriesList = [];
            $structureElement->feedbackFormsList = [];
            foreach ($languages as &$languageElement) {
                if ($languageElement->requested) {
                    $elementsList = $structureManager->getElementsFlatTree($languageElement->id, null, 'structure');
                    foreach ($elementsList as &$element) {
                        if ($element->structureType == 'gallery') {
                            $linksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'connectedGallery', 'parent');
                            $item = [];
                            $item['id'] = $element->id;
                            $item['title'] = $element->getTitle();
                            $item['select'] = isset($linksIndex[$element->id]);
                            $structureElement->galleriesList[] = $item;
                        } elseif ($element->structureType == 'feedback') {
                            $item = [];
                            $item['id'] = $element->id;
                            $item['title'] = $element->getTitle();
                            $item['select'] = $element->id == $structureElement->feedbackId;
                            $structureElement->feedbackFormsList[] = $item;
                        }
                    }
                }
            }

            if ($structureElement->final) {
                $structureElement->setTemplate('shared.content.tpl');
                $renderer = $this->getService('renderer');
                $renderer->assign('contentSubTemplate', 'component.form.tpl');
                $renderer->assign('form', $structureElement->getForm('form'));
            }
        }
    }
}