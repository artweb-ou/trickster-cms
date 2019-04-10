<?php

/**
 * Class receiveFilesShared
 *
 * @property FilesElementTrait $structureElement
 */
class receiveFilesShared extends structureElementAction
{
    protected $loggable = true;

    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param FilesElementTrait $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $propertyNames = $structureElement->getFileSelectorPropertyNames();
        foreach ($propertyNames as $propertyName) {
            if ($filesInfo = $structureElement->$propertyName) {
                $isPrivilegesSettingRequired = $structureElement->isPrivilegesSettingRequired();
                $pathsManager = $this->getService('PathsManager');
                $uploadsPath = $pathsManager->getPath('uploads');
                $cachePath = $pathsManager->getPath('uploadsCache');
                $privilegesManager = $this->getService('privilegesManager');
                $user = $this->getService('user');

                foreach ($filesInfo as &$fileInfo) {
                    $temporaryFile = $cachePath . basename($fileInfo['tmp_name']);
                    if (is_file($temporaryFile)) {
                        if ($fileElement = $structureManager->createElement(
                            'file',
                            'showForm',
                            $structureElement->getFilesParentElementId(),
                            false,
                            $structureElement->getConnectedFileType($propertyName)
                        )
                        ) {
                            if ($structureElement instanceof StructureElementUploadedFilesPathInterface) {
                                $folder = $structureElement->getUploadedFilesPath();
                            } else {
                                $folder = $uploadsPath;
                            }
                            $originalFileName = $fileInfo['name'];

                            $info = pathinfo($originalFileName);
                            $fileElement->title = str_replace('_', ' ', ucfirst(ucfirst($info['filename'])));
                            $fileElement->file = $fileElement->getId();
                            $fileElement->fileName = $originalFileName;

                            $fileElement->persistElementData();

                            copy($temporaryFile, $folder . $fileElement->file);
                            unlink($temporaryFile);
                            if ($isPrivilegesSettingRequired) {
                                $privilegesManager->setPrivilege($user->id, $structureElement->getId(), 'file', 'delete', 'allow');
                            }
                        }
                    }
                }
            }
        }
        if ($url = $structureElement->getFileUploadSuccessUrl()) {
            $controller->redirect($url);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = $this->structureElement->getFileSelectorPropertyNames();
    }
}

