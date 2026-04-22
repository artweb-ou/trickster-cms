<?php

class publishSocialPost extends structureElementAction
{
    /**
     * @param socialPostElement $structureElement
     */
    public function execute(structureManager $structureManager, controller $controller, structureElement $structureElement): void
    {
        $structureElement->setViewName('form');
        if ($structureElement->requested) {
            if ($controller->getParameter('plugin')) {
                $pluginId = $controller->getParameter('plugin');
                if ($pluginElement = $structureManager->getElementById($pluginId)) {
                    $info = [];
                    if ($structureElement->message) {
                        $info['message'] = $structureElement->message;
                    }
                    if ($structureElement->linkTitle) {
                        $info['linkTitle'] = $structureElement->linkTitle;
                    }
                    if ($structureElement->linkTitle) {
                        $info['linkDescription'] = $structureElement->linkDescription;
                    }
                    if ($structureElement->linkURL) {
                        $info['linkURL'] = $structureElement->linkURL;
                    }
                    if ($structureElement->originalName) {
                        $info['image'] = $controller->baseURL . 'image/type:socialImage/id:' . $structureElement->image . '/filename:' . $structureElement->originalName;
                    }

                    if ($pluginElement->makePost($info)) {
                        $structureElement->updateStatusInfo($pluginId, 'success');
                    } else {
                        $structureElement->updateStatusInfo($pluginId, 'error');
                    }
                }
            }
        }
        $controller->redirect($structureElement->URL);
    }
}


