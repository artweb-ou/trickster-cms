<?php

abstract class ProductFilter implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $productsListElement;
    protected $type = '';
    protected $optionsInfo;
    protected $initalOptions;

    public function __construct(ProductsListElement $element, $initalOptions = [])
    {
        $this->productsListElement = $element;
        $this->initalOptions = $initalOptions;
    }

    abstract public function getOptionsInfo();

    public function setOptionsInfo($optionsInfo)
    {
        $this->optionsInfo = $optionsInfo;
    }

    public function getTitle()
    {
        return $this->getService('translationsManager')->getTranslationByName('product_filter.' . $this->type);
    }

    public function getType()
    {
        return $this->type;
    }

    public function isRelevant()
    {
        return $this->getOptionsInfo() || $this->getArguments();
    }

    public function getId()
    {
        return $this->type;
    }

    public function getData()
    {
        return [
            'type' => $this->getType(),
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'options' => $this->getOptionsInfo(),
        ];
    }

    abstract protected function getArguments();
}