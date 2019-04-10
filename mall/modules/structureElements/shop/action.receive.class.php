<?php

class receiveShop extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $linksManager = $this->getService('linksManager');

            //persist shop data
            $structureElement->prepareActualData();

            if ($structureElement->structureName == '') {
                $structureElement->structureName = $structureElement->title;
            }
            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            }
            if (!is_null($structureElement->getDataChunk("photo")->originalName)) {
                $structureElement->photo = $structureElement->id . '_photo';
                $structureElement->photoOriginalName = $structureElement->getDataChunk("photo")->originalName;
            }
            $hoursFilled = false;
            foreach ($structureElement->customOpeningHours as $dayHours) {
                if ($dayHours['start'] || $dayHours['end']) {
                    $hoursFilled = true;
                    break;
                }
            }
            if (!$hoursFilled) {
                $structureElement->customOpeningHours = '';
            }
            $structureElement->persistElementData();

            //persist categories links
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id,
                $structureElement::LINK_TYPE_CATEGORY, 'child');
            $categoriesFolder = $structureManager->getElementByMarker('shopCategories');
            $categoriesList = $categoriesFolder->getChildrenList();

            foreach ($categoriesList as &$category) {
                if (isset($compiledLinks[$category->id]) && !in_array($category->id, $structureElement->categories)) {
                    $compiledLinks[$category->id]->delete();
                } elseif (!isset($compiledLinks[$category->id]) && in_array($category->id, $structureElement->categories)) {
                    $linksManager->linkElements($category->id, $structureElement->id,
                        $structureElement::LINK_TYPE_CATEGORY);
                }
            }

            //persist campaigns links
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id, 'campaigns', 'parent');
            $campaignsFolder = $structureManager->getElementByMarker('campaigns');
            $campaignsList = $structureManager->getElementsFlatTree($campaignsFolder->id);

            foreach ($campaignsList as &$campaign) {
                if (isset($compiledLinks[$campaign->id]) && !in_array($campaign->id, $structureElement->campaigns)) {
                    $compiledLinks[$campaign->id]->delete();
                } else {
                    if (!isset($compiledLinks[$campaign->id]) && in_array($campaign->id, $structureElement->campaigns)) {
                        $linksManager->linkElements($structureElement->id, $campaign->id, 'campaigns', true);
                    }
                }
            }

            //persist rooms links
            $compiledLinks = $linksManager->getElementsLinksIndex($structureElement->id,
                $structureElement::LINK_TYPE_ROOM, 'parent');
            $floorsElement = $structureManager->getElementByMarker('floors');
            if ($floorsElement) {
                $floorElements = $floorsElement->getChildrenList();
                foreach ($floorElements as $floorElement) {
                    foreach ($floorElement->getRooms() as $roomElement) {
                        if (isset($compiledLinks[$roomElement->id])
                            && !in_array($roomElement->id, $structureElement->rooms)
                        ) {
                            $compiledLinks[$roomElement->id]->delete();
                        } else {
                            if (!isset($compiledLinks[$roomElement->id])
                                && in_array($roomElement->id, $structureElement->rooms)
                            ) {
                                $linksManager->linkElements($structureElement->id, $roomElement->id,
                                    $structureElement::LINK_TYPE_ROOM, true);
                            }
                        }
                    }
                }
            }
            $connectedOpeningHoursGroupId = $structureElement->getConnectedOpeningHoursGroupId();
            if ($connectedOpeningHoursGroupId
                && $connectedOpeningHoursGroupId != $structureElement->openingHoursGroupId
            ) {
                $linksManager->unLinkElements($structureElement->id, $connectedOpeningHoursGroupId
                    , shopElement::LINK_TYPE_OPENING_HOURS);
            }
            if ($structureElement->openingHoursGroupId) {
                $linksManager->linkElements($structureElement->id, $structureElement->openingHoursGroupId
                    , shopElement::LINK_TYPE_OPENING_HOURS);
            }

            $controller->redirect($structureElement->URL);
        } else {
            $structureElement->executeAction('showForm');
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'structureName',
            'image',
            'photo',
            'categories',
            'campaigns',
            'rooms',
            'roomId',
            'openingHoursGroupId',
            'customOpeningHours',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}

