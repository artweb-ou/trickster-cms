<?php

class productImportTemplateColumnElement extends structureElement
{
    protected $allowedTypes = [];
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_productimporttemplatecolumn';
    public $defaultActionName = 'showForm';
    public $role = 'container';
    protected $productParameter;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['productVariable'] = 'text';
        $moduleStructure['productParameterId'] = 'text';
        $moduleStructure['columnNumber'] = 'text';
        $moduleStructure['mandatory'] = 'checkbox';
    }

    public function getProductParameter()
    {
        if (is_null($this->productParameter)) {
            $this->productParameter = false;
            if ($this->productParameterId) {
                $structureManager = $this->getService('structureManager');
                $this->productParameter = $structureManager->getElementById($this->productParameterId);
            }
        }
        return $this->productParameter;
    }

    public function getSelectedProductParameterTitle()
    {
        $parameterTitle = "";
        if ($parameter = $this->getProductParameter()) {
            $parameterTitle = $parameter->title . " (" . $parameter->getParameterGroup()->title . ")";
        }
        return $parameterTitle;
    }

    public function charToNum($chars)
    {
        $alphabet = $this->getAlphabet();
        $result = 0;
        for ($i = strlen($chars) - 1; $i >= 0; $i--) {
            $char = strtolower(substr($chars, $i, 1));
            $value = array_search($char, $alphabet) + 1;
            $result = $value * pow(26, strlen($chars) - 1 - $i) + $result;
        }
        return $result;
    }

    public function numToChar($number)
    {
        $result = '';
        $alphabet = $this->getAlphabet();
        $number = base_convert($number, 10, 26);

        for ($i = 0; $i < strlen($number); $i++) {
            $char = strtolower(substr($number, $i, 1));
            $value = base_convert($char, 26, 10);
            if (isset($alphabet[$value - 1])) {
                $result .= strtoupper($alphabet[$value - 1]);
            }
        }

        return $result;
    }

    protected function getAlphabet()
    {
        return [
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
        ];
    }
}


