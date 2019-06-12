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
        $moduleStructure['streetViewControlEnabled'] = 'checkbox';
        $moduleStructure['mapTypeControlEnabled'] = 'checkbox';
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

    public function getJsonMapInfo()
    {
        $styles = $this->styles ? json_decode($this->styles) : [];
        return json_encode([
            'coordinates' => $this->coordinates,
            'title' => $this->title,
            'content' => $this->description,
            'heightAdjusted' => true,
            'styles' => $styles,
            'zoomControlEnabled' => !!$this->zoomControlEnabled,
            'streetViewControlEnabled' => !!$this->streetViewControlEnabled,
            'mapTypeControlEnabled' => !!$this->mapTypeControlEnabled,
        ]);
    }
    public function getJsonMapIframeInfo()
    {
        return json_encode([
            'coordinates' => $this->coordinates,
            'heightAdjusted' => true,
            'title' => $this->title,
        ]);
    }
}