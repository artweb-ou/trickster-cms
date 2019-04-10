<?php

class naturalNumberDataChunk extends DataChunk implements ElementStorageValueHolderInterface
{
    use ElementStorageValueDataChunkTrait;

    public function convertStorageToDisplay()
    {
        $this->displayValue = $this->storageValue;
    }

    public function convertStorageToForm()
    {
        $this->formValue = $this->storageValue;
    }

    public function convertFormToStorage()
    {
        $this->setStorageValue(intval($this->formValue));
    }

    public function setExternalValue($value)
    {
        $this->storageValue = intval($value);
        $this->formValue = null;
        $this->convertStorageToDisplay();
    }
}


