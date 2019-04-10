<?php

class importCalculationsRuleElement extends structureElement
{
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_importcalculationsrule';
    public $defaultActionName = 'showForm';
    public $role = 'content';
    protected $conditionsIndex;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['rules'] = 'serializedIndex';
        $moduleStructure['priceModifier'] = 'text';
        $moduleStructure['action'] = 'text';
    }

    public function getJsonInfo()
    {
        $informativeRules = [];
        $structureManager = $this->getService('structureManager');
        $rules = $this->rules;
        foreach ($rules as &$rule) {
            $informativeRule = [
                'type' => $rule['type'],
                'value' => [],
            ];
            if ($rule['type'] != 'price' && $rule['type'] != 'import_plugin') {
                foreach ($rule['value'] as &$value) {
                    $title = $value;
                    if ($element = $structureManager->getElementById($value)) {
                        $title = $element->title;
                    }
                    $informativeRule['value'][] = [
                        'title' => $title,
                        'value' => $value,
                    ];
                }
            } else {
                $informativeRule['value'] = $rule['value'];
            }
            $informativeRules[] = $informativeRule;
        }
        $importPluginsNames = [];
        $pluginsFolder = $structureManager->getElementByMarker('importPlugins');
        if ($pluginsFolder) {
            $importPlugins = $pluginsFolder->getChildrenList();
            foreach ($importPlugins as &$importPlugin) {
                $importPluginsNames[] = $importPlugin->getName();
            }
        }
        $data = [
            'title' => $this->title,
            'rules' => $informativeRules,
            'priceModifier' => $this->priceModifier,
            'plugins' => $importPluginsNames,
        ];
        return json_encode($data);
    }

    public function matchProduct($productId, $originalPrice, $importOrigin = '')
    {
        $result = count($this->rules) > 0 && (!$importOrigin || $this->matchCondition('import_plugin', $importOrigin));
        if ($result) {
            $linksManager = $this->getService('linksManager');
            if ($result && $this->hasCondition('product')) {
                $result = $this->matchCondition('product', $productId);
            }
            if ($result && $this->hasCondition('brand')) {
                $brandIds = $linksManager->getConnectedIdList($productId, 'productbrand', 'child');
                $brandId = (int)reset($brandIds);
                $result = $this->matchCondition('brand', $brandId);
            }
            if ($result && $this->hasCondition('category')) {
                $result = false;
                $categoriesIds = $linksManager->getConnectedIdList($productId, 'catalogue', 'child');
                foreach ($categoriesIds as &$categoryId) {
                    if ($this->matchCondition('category', $categoryId)) {
                        $result = true;
                        break;
                    }
                }
            }
            if ($result && $this->hasCondition('price')) {
                $result = false;
                foreach ($this->rules as &$condition) {
                    $type = $condition['type'];
                    if ($type != 'price' || !$condition['value'] || count($condition['value']) < 2) {
                        continue;
                    }
                    $conditionStart = $this->parsePriceFromCondition($condition['value'][0]);
                    $conditionEnd = $this->parsePriceFromCondition($condition['value'][1]);
                    if ($originalPrice >= $conditionStart && $originalPrice < $conditionEnd) {
                        $result = true;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    public function matchCondition($type, $value)
    {
        $this->getConditionsIndex();
        return empty($this->conditionsIndex[$type]) || isset($this->conditionsIndex[$type][$value]);
    }

    public function hasCondition($type)
    {
        $this->getConditionsIndex();
        return !empty($this->conditionsIndex[$type]);
    }

    public function getModifier()
    {
        return (int)str_replace('%', '', $this->priceModifier) / 100;
    }

    protected function getConditionsIndex()
    {
        if ($this->conditionsIndex === null) {
            foreach ($this->rules as $condition) {
                if ($condition['value']) {
                    $type = $condition['type'];
                    if (!isset($this->conditionsIndex[$type])) {
                        $this->conditionsIndex[$type] = [];
                    }
                    if (is_array($condition['value'])) {
                        $this->conditionsIndex[$type] += array_flip($condition['value']);
                    } else {
                        $this->conditionsIndex[$type][$condition['value']] = true;
                    }
                }
            }
        }
        return $this->conditionsIndex;
    }

    protected function parsePriceFromCondition($input)
    {
        return (float)str_replace(',', '.', $input);
    }
}

