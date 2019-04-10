<?php

class pasteElementsRoot extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $user = $this->getService('user');
        $structureElement->executeAction('showFullList');
        if ($contentType = $controller->getParameter('view')) {
            $renderer = $this->getService('renderer');
            $renderer->assign('contentType', $contentType);
            $structureManager->setNewElementLinkType($contentType);
        }
        if ($navigateId = $controller->getParameter('navigateId')) {
            $copyInformation = $user->getStorageAttribute('copyInformation');
            $moveInformation = $user->getStorageAttribute('moveInformation');

            if ($navigatedElement = $structureManager->getElementById($navigateId)) {
                $targetUrl = $navigatedElement->URL;
                if ($contentType) {
                    $targetUrl .= 'view:' . $contentType . '/';
                }

                if ($copyInformation && count($copyInformation['elementsToCopy']) > 0
                ) {
                    if ($copyData = $structureManager->copyElements($copyInformation['elementsToCopy'], $navigateId, [
                        'structure',
                        'headerContent',
                        'leftColumn',
                        'rightColumn',
                        'bottomMenu',
                    ], $contentType)
                    ) {
                        if ($navigateId == $copyInformation['sourceId']) {
                            $this->renameCopies($copyInformation['elementsToCopy'], $copyData);
                        }
                    }
                    $controller->redirect($targetUrl);
                } elseif ($moveInformation && count($moveInformation['elementsToMove']) > 0
                ) {
                    if ($structureManager->moveElements($moveInformation['elementsToMove'], $navigateId, [
                        'structure',
                        'headerContent',
                        'leftColumn',
                        'rightColumn',
                        'bottomMenu',
                    ])
                    ) {
                        $controller->redirect($targetUrl);
                    }
                }
            }
        }
    }

    protected function renameCopies($topLevel, $copiesData)
    {
        $structureManager = $this->getService('structureManager');
        foreach ($topLevel as &$originalElementId) {
            if (isset($copiesData[$originalElementId])) {
                $renamedElementId = $copiesData[$originalElementId];
                if ($renamedElement = $structureManager->getElementById($renamedElementId)) {
                    if ($renamedElement->title) {
                        foreach ($renamedElement->getLanguagesList() as $languageId) {
                            $renamedElement->setValue("title", $renamedElement->getLanguageValue('title', $languageId) . ' copy', $languageId);
                        }
                        $renamedElement->persistElementData();
                    }
                }
            }
        }
    }
}

