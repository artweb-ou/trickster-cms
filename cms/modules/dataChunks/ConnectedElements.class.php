<?php

class ConnectedElementsDataChunk extends DataChunk implements ElementHolderInterface, ExtraDataHolderDataChunkInterface
{
    use ElementHolderDataChunkTrait;
    protected $ids;
    protected $role = 'parent';
    protected $linkType;

    public function setFormValue($value)
    {
        $value = (array)$value;
        $this->formValue = $this->loadElements($value);
    }

    public function getStorageValue()
    {
        if ($this->storageValue === null) {
            $this->loadStorageValue();
        }
        return $this->storageValue;
    }

    public function setExternalValue($value)
    {
        $this->formValue = null;
        $this->displayValue = null;
        $this->storageValue = (array)$value;
    }

    protected function loadStorageValue()
    {
        $this->storageValue = [];

        if ($ids = $this->getIds()) {
            $this->storageValue = $ids;
        }
    }

    protected function getIds()
    {
        if ($this->ids === null) {
            $this->ids = [];
            /**
             * @var linksManager $linksManager
             */
            if ($linksManager = $this->getService('linksManager')) {
                if ($this->ids = $linksManager->getConnectedIdList(
                    $this->structureElement->id,
                    $this->linkType,
                    $this->role
                )) {
                    return $this->ids;
                }
            }
        }
        return $this->ids;
    }

    public function convertFormToStorage()
    {
        $this->storageValue = [];
        foreach ($this->formValue as $element) {
            $this->storageValue[] = $element->id;
        }
        $this->displayValue = $this->formValue;
    }

    public function convertStorageToDisplay()
    {
        if ($this->storageValue === null) {
            $this->loadStorageValue();
        }
        $this->displayValue = $this->loadElements($this->storageValue);
    }

    public function convertStorageToForm()
    {
        if ($this->storageValue === null) {
            $this->loadStorageValue();
        }
        $this->formValue = $this->loadElements($this->storageValue);
    }

    protected function loadElements($ids)
    {
        $elements = [];
        if (is_array($ids)) {
            /**
             * @var structureManager $structureManager
             */
            $structureManager = $this->getService('structureManager');
            foreach ($ids as $id) {
                if ($element = $structureManager->getElementById($id)) {
                    $elements[] = $element;
                }
            }
        }
        return $elements;
    }

    public function persistExtraData()
    {
        if ($this->storageValue === null) {
            //this chunk wasn't modified at all, no need to load it and save it again.
            return;
        }
        /**
         * @var linksManager $linksManager
         */
        if ($linksManager = $this->getService('linksManager')) {
            $linksIndex = $linksManager->getElementsLinksIndex($this->structureElement->getId(), $this->linkType, $this->role);
            foreach ($this->storageValue as $connectedId) {
                if (!isset($linksIndex[$connectedId])) {
                    if ($this->role === 'child') {
                        $linksManager->linkElements($connectedId, $this->structureElement->getId(), $this->linkType);
                    } else {
                        $linksManager->linkElements($this->structureElement->getId(), $connectedId, $this->linkType);
                    }
                }
                unset($linksIndex[$connectedId]);
            }
            foreach ($linksIndex as $key => &$link) {
                $link->delete();
            }
        }
    }

    public function deleteExtraData()
    {
        /**
         * @var linksManager $linksManager
         */
        if ($linksManager = $this->getService('linksManager')) {
            $linksIndex = $linksManager->getElementsLinksIndex($this->structureElement->getId(), $this->linkType, $this->role);
            foreach ($linksIndex as $key => &$link) {
                $link->delete();
            }
        }
    }

    public function copyExtraData($oldValue, $oldId, $newId)
    {
        /**
         * @var linksManager $linksManager
         */
        if ($linksManager = $this->getService('linksManager')) {
            $ids = $linksManager->getConnectedIdList($oldId, $this->linkType, $this->role);
            foreach ($ids as $id) {
                if ($this->role === 'child') {
                    $linksManager->linkElements($id, $newId, $this->linkType);
                } else {
                    $linksManager->linkElements($newId, $id, $this->linkType);
                }
            }
        }
    }
}