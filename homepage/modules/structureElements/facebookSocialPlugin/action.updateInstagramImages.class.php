<?php

class updateInstagramImagesFacebookSocialPlugin extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        if ($structureElement->requested) {
            if ($structureElement->final) {
                if (!empty($controller->getParameter('imagesdata'))) {
                    $existedImages = $structureElement->getInstagramImages();
                    $existedImagesIds = [];
                    foreach ($existedImages as $existedImage) {
                        $existedImagesIds[$existedImage->instagramId] = $existedImage;
                    }
                    foreach (unserialize(urldecode($controller->getParameter('imagesdata'))) as $image) {
                        if (isset($existedImagesIds[$image['id']])) {
                            $existedImageElement = $existedImagesIds[$image['id']];
                            $existedImageElement->prepareActualData();
                            $existedImageElement->importExternalData([
                                'image' => $image['image'],
                            ]);
                            $existedImageElement->persistElementData();
                            unset($existedImagesIds[$image['id']]);
                        } else {
                            $newImageElement = $structureManager->createElement('instagramImage', 'show', $structureElement->id);
                            $newImageElement->prepareActualData();

                            $newImageElement->importExternalData([
                                'instagramId' => $image['id'],
                                'image' => $image['image'],
                                'pageSocialId' => $image['pageSocialId'],
                            ]);
                            $newImageElement->persistElementData();
                        }
                    }
                    foreach($existedImagesIds as $elementToDelete) {
                        $elementToDelete->deleteElementData();
                    }
                }

                $controller->redirect($structureElement->URL . 'id:' . $structureElement->id . '/action:showInstagramImages/');
            }
        }
    }
}