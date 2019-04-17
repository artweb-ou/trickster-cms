<?php

abstract class productFilter implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $productsListElement;
    protected $type = '';
    protected $options;
    protected $initalOptions;

    public function __construct(ProductsListStructureElement $element, $initalOptions = [])
    {
        $this->productsListElement = $element;
        $this->initalOptions = $initalOptions;
    }

    public abstract function getOptionsInfo();

//    protected abstract function loadRelatedIds();

//    public function addFilter(productFilter $filter)
//    {
//        if ($this->nextFilter === null) {
//            $this->nextFilter = $filter;
//        } else {
//            $this->nextFilter->addFilter($filter);
//        }
//    }
//
//    public function apply(array &$productsIds = [])
//    {
//        if (!$this->passive) {
//            $this->inspectFiltrationChain($productsIds);
//        }
//        if ($productsIds) {
//            $this->filter($productsIds);
//        }
//        if ($this->nextFilter !== null) {
//            $this->nextFilter->apply($productsIds);
//        }
//    }
//
//    protected function inspectFiltrationChain(array $productsIds)
//    {
//        for ($nextFilter = $this->nextFilter; $nextFilter !== null && count($productsIds) > 0; $nextFilter = $nextFilter->getNextFilter()) {
//            $nextFilter->filter($productsIds);
//        }
//        $this->limitOptions($productsIds);
//    }
//
//    protected function limitOptions(array $productsIds)
//    {
//        if (!$productsIds) {
//            $this->options = [];
//        }
//    }
//
//    public function filter(array &$ids = [])
//    {
//        if ($this->arguments) {
//            if ($this->relatedIds === null) {
//                $this->loadRelatedIds();
//            }
//            $ids = array_intersect($ids, $this->relatedIds);
//        }
//    }
//
//    public function getNextFilter()
//    {
//        return $this->nextFilter;
//    }
//
//    public function setNextFilter(self $filter)
//    {
//        $this->nextFilter = $filter;
//    }
//
//    public function getArguments()
//    {
//        return $this->arguments;
//    }
//
//    public function getOptions()
//    {
//        return $this->options;
//    }
//
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

    abstract protected function getArguments();
}