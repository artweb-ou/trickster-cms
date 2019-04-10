<?php

class receiveOptionsImagesFormProduct extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $imagesManager = $this->getService('ProductOptionsImagesManager');
            $existingInfo = $structureElement->getOptionsImagesInfo();
            $records = [];
            $unchangedOptions = [];
            foreach ($structureElement->optionsImagesInput as $optionId => $imageId) {
                $imageId = trim($imageId);
                if (!$imageId) {
                    continue;
                }
                if (isset($existingInfo[$optionId]) && $imageId == $existingInfo[$optionId]) {
                    $unchangedOptions[] = $optionId;
                    continue;
                }
                $records[] = [
                    'productId' => $structureElement->id,
                    'selectionValue' => $optionId,
                    'image' => $imageId,
                ];
            }
            $cleanupQuery = $imagesManager->queryDb()->where('productId', '=', $structureElement->id);
            if ($unchangedOptions) {
                $cleanupQuery->whereNotIn('selectionValue', $unchangedOptions);
            }
            $cleanupQuery->delete();
            if ($records) {
                $imagesManager->insertRecords($records);
            }
            $url = $structureElement->URL . 'id:' . $structureElement->id
                . '/action:showOptionsImagesForm/';
            $controller->redirect($url);
        }
        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'optionsImagesInput',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}
