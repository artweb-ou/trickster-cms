<?php

class showFormShop extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->final) {
            $externalCategoryId = false;
            if (isset($controller->foundParameters['categoryId'])) {
                $externalCategoryId = intval($controller->foundParameters['categoryId']);
            }
            //PREPARE CATEGORIES SELECTOR
            $structureElement->categoriesList = [];
            $categoriesFolder = $structureManager->getElementByMarker('shopCategories');
            $linksManager = $this->getService('linksManager');
            if ($categoriesFolder) {
                $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id,
                    $structureElement::LINK_TYPE_CATEGORY, 'child');
                $categoriesList = $categoriesFolder->getChildrenList();

                foreach ($categoriesList as &$category) {
                    $categoryItem = [];
                    $categoryItem['categoryLevel'] = $category->level - 3;

                    $categoryItem['select'] = isset($compiledLinks[$category->id]) || $externalCategoryId == $category->id;
                    $categoryItem['title'] = $category->title;
                    $categoryItem['structureName'] = $category->structureName;
                    $categoryItem['id'] = $category->id;

                    $structureElement->categoriesList[] = $categoryItem;
                }
            }

            $structureElement->campaignsList = [];
            $campaignsFolder = $structureManager->getElementByMarker('campaigns');
            if ($campaignsFolder) {
                $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'campaigns', 'parent');
                $campaignsList = $structureManager->getElementsFlatTree($campaignsFolder->id);

                foreach ($campaignsList as &$campaign) {
                    $campaignItem = [];
                    $campaignItem['campaignLevel'] = $campaign->level - 3;

                    $campaignItem['select'] = isset($compiledLinks[$campaign->id]);
                    $campaignItem['title'] = $campaign->title;
                    $campaignItem['structureName'] = $campaign->structureName;
                    $campaignItem['id'] = $campaign->id;

                    $structureElement->campaignsList[] = $campaignItem;
                }
            }

            $structureElement->floorsList = [];
            $floorsFolder = $structureManager->getElementByMarker('floors');
            if ($floorsFolder) {
                $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id,
                    $structureElement::LINK_TYPE_ROOM, 'parent');
                $floorsList = $floorsFolder->getChildrenList();

                foreach ($floorsList as $floorElement) {
                    $floorInfo = [
                        'title' => $floorElement->getTitle(),
                        'id' => $floorElement->id,
                        'rooms' => [],
                    ];
                    foreach ($floorElement->getRooms() as $roomElement) {
                        $floorInfo['rooms'][] = [
                            'title' => $roomElement->getTitle(),
                            'select' => isset($compiledLinks[$roomElement->id]),
                            'id' => $roomElement->id,
                        ];
                    }
                    $structureElement->floorsList[] = $floorInfo;
                }
            }
            $openingHoursGroupElements = [];
            $openingHoursElement = $structureManager->getElementByMarker('openingHours');
            if ($openingHoursElement) {
                $openingHoursGroupElements = $openingHoursElement->getChildrenList();
                $connectedOpeningHoursGroupElementId = $structureElement->getConnectedOpeningHoursGroupId();
                foreach ($openingHoursGroupElements as $openingHoursGroupElement) {
                    $openingHoursGroupElement->connected =
                        $connectedOpeningHoursGroupElementId == $openingHoursGroupElement->id;
                }
            }
            $structureElement->setTemplate('shared.content.tpl');
            $renderer = renderer::getInstance();
            $renderer->assign('contentSubTemplate', 'component.form.tpl');
            $renderer->assign('form', $structureElement->getForm('form'));
            $renderer->assign('formFieldName', 'customOpeningHours');
            $renderer->assign('openingHoursGroupElements', $openingHoursGroupElements);
        }
        $structureElement->setViewName('form');
    }
}