<?php

class serializedArrayDataChunk extends DataChunk implements ElementStorageValueHolderInterface
{
    use ElementStorageValueDataChunkTrait;

    public function convertStorageToDisplay()
    {
        if ($this->storageValue != '') {
            $this->displayValue = unserialize($this->storageValue);
            if (!$this->displayValue) {
                $this->displayValue = [$this->storageValue => true];
            }
        } else {
            $this->displayValue = [];
        }
    }

    public function convertStorageToForm()
    {
        if ($this->storageValue) {
            $this->formValue = unserialize($this->storageValue);
            if (!$this->formValue) {
                $this->formValue = [$this->storageValue => true];
            }
        }
    }

    public function convertFormToStorage()
    {
        if (is_array($this->formValue)) {
            $list = array_flip($this->formValue);
            foreach ($list as $key => &$value) {
                if ($key == '') {
                    unset($list[$key]);
                } else {
                    $value = true;
                }
            }
        } else {
            $list = [];
        }
        $storageValue = serialize($list);
        $this->setStorageValue($storageValue);
    }

    public function setExternalValue($value)
    {
        $this->storageValue = $value;
        $this->formValue = null;
        $this->convertStorageToDisplay();
    }
}

