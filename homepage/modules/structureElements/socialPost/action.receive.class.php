<?php

use App\Paths\PathsManager;

class receiveSocialPost extends structureElementAction
{
    protected $loggable = true;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($this->validated) {
            $structureElement->prepareActualData();

            if (!is_null($structureElement->getDataChunk("image")->originalName)) {
                $structureElement->image = $structureElement->id;
                $structureElement->originalName = $structureElement->getDataChunk("image")->originalName;
            } else {
                if ($structureElement->replacementImage) {
                    $pathsManager = $this->getService(PathsManager::class);
                    $path = $pathsManager->getPath('uploads');
                    $oldFile = $path . $structureElement->replacementImage;
                    $newFile = $path . $structureElement->id;
                    if (file_exists($oldFile) && is_file($oldFile)) {
                        copy($oldFile, $newFile);
                    }
                    $structureElement->image = $structureElement->id;
                    $structureElement->originalName = $structureElement->id;
                }
            }
            $structureElement->structureName = $structureElement->title;

            $structureElement->persistElementData();
            $controller->redirect($structureElement->URL);
        }

        $structureElement->executeAction("showForm");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'title',
            'linkTitle',
            'linkDescription',
            'linkURL',
            'message',
            'image',
            'replacementImage',
        ];
    }

    public function setValidators(&$validators)
    {
    }
}


