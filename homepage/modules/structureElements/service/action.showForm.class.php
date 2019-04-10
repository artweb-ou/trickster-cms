<?php

class showFormService extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            $linksManager = $this->getService('linksManager');
            $marker = $this->getService('ConfigManager')->get('main.rootMarkerPublic');
            $publicRoot = $structureManager->getElementByMarker($marker);
            $languages = $structureManager->getElementsChildren($publicRoot->id);
            $structureElement->feedbackFormsList = [];
            foreach ($languages as &$languageElement) {
                if ($languageElement->requested) {
                    $elementsList = $structureManager->getElementsByType('feedback', $languageElement->id);
                    foreach ($elementsList as &$element) {
                        if ($element->structureType == 'feedback') {
                            $formData = $structureElement->getFormData();
                            $feedbackId = $formData['feedbackId'];
                            $item = [];
                            $item['id'] = $element->id;
                            $item['title'] = $element->getTitle();
                            $item['select'] = $element->id == $feedbackId;
                            $structureElement->feedbackFormsList[] = $item;
                        }
                    }
                }
            }
            if ($galleriesList = $structureElement->getConnectedGalleries()) {
                $galleries = [];
                $linksIndex = $linksManager->getElementsLinksIndex($structureElement->id, 'connectedGallery', 'child');
                foreach ($galleriesList as &$element) {
                    $item['id'] = $element->id;
                    $item['title'] = $element->getTitle();
                    $item['select'] = isset($linksIndex[$element->id]);
                    $galleries[] = $item;
                }
                $structureElement->galleries = $galleries;
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