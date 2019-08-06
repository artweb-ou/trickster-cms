<?php

class updateSocialPagesShared extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            if ($structureElement->final) {
                if (!empty($controller->getParameter('pagesdata'))) {
                    $existedSocialPages = $structureElement->getChildrenList(null, 'structure', 'socialPage');
                    $existedSocialPagesSocialIds = [];
                    foreach ($existedSocialPages as $socialPage) {
                        $existedSocialPagesSocialIds[$socialPage->socialId] = $socialPage;
                    }
                    foreach (unserialize(urldecode($controller->getParameter('pagesdata'))) as $page) {
                        if (isset($existedSocialPagesSocialIds[$page['socialId']])) {
                            $existedPageElement = $existedSocialPagesSocialIds[$page['socialId']];
                            $existedPageElement->prepareActualData();
                            $existedPageElement->importExternalData([
                                'title' => $page['title'],
                            ]);
                            $existedPageElement->persistElementData();
                            unset($existedSocialPagesSocialIds[$page['socialId']]);
                        } else {
                            $newPageElement = $structureManager->createElement('socialPage', 'show', $structureElement->id);
                            $newPageElement->prepareActualData();

                            $newPageElement->importExternalData([
                                'title' => $page['title'],
                                'socialId' => $page['socialId'],
                            ]);
                            $newPageElement->persistElementData();
                        }
                    }
                    foreach($existedSocialPagesSocialIds as $elementToDelete) {
                        $elementToDelete->deleteElementData();
                    }
                }

                $controller->redirect($structureElement->URL . 'id:' . $structureElement->id . '/action:showSocialPages/');
            }
        }
    }
}