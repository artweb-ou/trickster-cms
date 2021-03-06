<?php

class ParameterProductFilter extends ProductFilter
{
    protected $type = 'parameter';
    protected $selectionElement;

    public function __construct(ProductsListElement $element, $initalOptions = [])
    {
        parent::__construct($element, $initalOptions);
        $this->selectionElement = $initalOptions['selectionElement'];
    }

    public function getSelectionId()
    {
        if ($selectionElement = $this->getSelectionElement()) {
            $id = $this->selectionElement->id;
        }

        return $id;
    }

    public function getId()
    {
        return $this->getSelectionId();
    }

    public function getTitle()
    {
        $title = '';
        if ($selectionElement = $this->getSelectionElement()) {
            $title = $selectionElement->title;
        }
        return $title;
    }

    public function getSelectionElement()
    {
        return $this->selectionElement;
    }

    public function getOptionsInfo()
    {
        if ($this->optionsInfo === null) {
            $this->optionsInfo = [];
            if ($valueElements = $this->productsListElement->getProductsListSelectionValues($this->getSelectionId())) {
                $argumentMap = $this->getArguments();

                foreach ($valueElements as $valueElement) {
                    if (!$valueElement->hidden) {
                        $this->optionsInfo[] = [
                            'title' => $valueElement->title,
                            'selected' => isset($argumentMap[$valueElement->id]),
                            'id' => $valueElement->id,
                        ];
                    }
                }
            }
        }
        return $this->optionsInfo;
    }

    protected function getArguments()
    {
        return array_flip($this->productsListElement->getFilterParameterValueIds());
    }
}