<?php

class mapElement extends menuDependantStructureElement implements ConfigurableLayoutsProviderInterface
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_map';
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hideTitle'] = 'checkbox';
        $moduleStructure['content'] = 'html';
        $moduleStructure['mapCode'] = 'code';
        $moduleStructure['country'] = 'text';
        $moduleStructure['region'] = 'text';
        $moduleStructure['city'] = 'text';
        $moduleStructure['address'] = 'text';
        $moduleStructure['zip'] = 'text';
        $moduleStructure['coordinates'] = 'text';
        $moduleStructure['description'] = 'text';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['styles'] = 'structure';
        $moduleStructure['zoomControlEnabled'] = 'checkbox';
        $moduleStructure['zoomLevel'] = 'text';
        $moduleStructure['streetViewControlEnabled'] = 'checkbox';
        $moduleStructure['mapTypeControlEnabled'] = 'checkbox';
        $moduleStructure['colorLayout'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showLayoutForm',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getFullAddress()
    {
        $fullAddress = '';
        if ($this->address) {
            $fullAddress .= $this->address;
        }

        if ($this->zip) {
            $fullAddress .= ", " . $this->zip;
        }
        if ($this->city) {
            if ($this->zip) {
                $fullAddress .= " ";
            } else {
                $fullAddress .= ", ";
            }
            $fullAddress .= $this->city;
        }
        if ($this->region) {
            $fullAddress .= "<br/>" . $this->region;
        }
        if ($this->country) {
            $fullAddress .= ", " . $this->country;
        }

        return $fullAddress;
    }

    public function getAdvancedAddressItems($itemTag='em', $items)
    {
        $fullAddress = '';
        if ($this->address && in_array('address', $items)) {
            $fullAddress .= "<{$itemTag} class='address'>" . $this->address . "</{$itemTag}>";
        }

        if ($this->zip && in_array('zip', $items)) {
            $fullAddress .=  "<{$itemTag} class='zip'>" . $this->zip . "</{$itemTag}>";
        }
        if ($this->city && in_array('city', $items)) {
            $fullAddress .= "<{$itemTag} class='city'>" . $this->city . "</{$itemTag}>";
        }
        if ($this->region && in_array('region', $items)) {
            $fullAddress .= "<{$itemTag} class='region'>" . $this->region . "</{$itemTag}>";
        }
        if ($this->country && in_array('country', $items)) {
            $fullAddress .= "<{$itemTag} class='country'>" . $this->country. "</{$itemTag}>";
        }

        return $fullAddress;
    }
    public function getJsonMapInfo()
    {
        return json_encode([
            'coordinates' => $this->coordinates,
            'title' => $this->title,
            'zoomLevel' => $this->zoomLevel,
            'content' => $this->description,
            'mapCode' => $this->mapCode,
            'heightAdjusted' => true,
            'styles' => trim($this->styles),
            'zoomControlEnabled' => !!$this->zoomControlEnabled,
            'streetViewControlEnabled' => !!$this->streetViewControlEnabled,
            'mapTypeControlEnabled' => !!$this->mapTypeControlEnabled,
        ]);
    }
}