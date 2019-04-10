<?php

class fileNameDataChunk extends DataChunk implements ElementStorageValueHolderInterface
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
        $this->setStorageValue(urlencode($this->formValue));
    }

    public function setExternalValue($value)
    {
        $this->storageValue = urlencode(urldecode($value));
        $this->formValue = null;
        $this->convertStorageToDisplay();
    }
}

