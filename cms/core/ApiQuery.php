<?php

class ApiQuery extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    protected $filtrationParameters = [];
    protected $resultTypes = [];

    /**
     * @return array
     */
    public function getResultTypes()
    {
        return $this->resultTypes;
    }

    protected $filteredIdLists = false;
    protected $queryResult = false;
    protected $exportType;
    protected $limit;
    protected $order;
    protected $start;
    protected $optimized = true;

    /**
     * @param boolean $optimized
     */
    public function setOptimized($optimized)
    {
        $this->optimized = $optimized;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param $exportType
     * @return $this
     */
    public function setExportType($exportType)
    {
        $this->exportType = $exportType;
        return $this;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @param $parameters
     * @return $this
     */
    public function setFiltrationParameters($parameters)
    {
        $this->filtrationParameters = $parameters;
        return $this;
    }

    public function setResultTypes($types)
    {
        $this->resultTypes = $types;
        return $this;
    }

    protected function performFiltration()
    {
        $queryFiltersManager = $this->getService('queryFiltersManager');
        $this->filteredIdLists = $queryFiltersManager->getFilterIdLists(
            $this->filtrationParameters,
            $this->resultTypes,
            $this->optimized
        );

        return $this->filteredIdLists;
    }

    public function getFilteredIdLists()
    {
        if (!$this->filteredIdLists) {
            if (!$this->resultTypes) {
                $this->resultTypes = [$this->exportType];
            }
            $this->performFiltration();
        }
        return $this->filteredIdLists;
    }

    public function getQueryResult()
    {
        if (!$this->queryResult) {
            $this->queryResult = [];
            $this->queryResult['start'] = $this->start;
            $this->queryResult['limit'] = $this->limit;

            if (!$this->resultTypes) {
                $this->resultTypes = [$this->exportType];
            }

            //no need to get all IDs for objects if there are no filters
            $filterIdLists = [$this->exportType => null];
            if ($this->filtrationParameters) {
                $filterIdLists = $this->getFilteredIdLists();
            }
            $apiQueryResultResolver = $this->getService('ApiQueryResultResolver');
            $this->queryResult = $apiQueryResultResolver->resolve(
                $filterIdLists,
                $this->exportType,
                $this->resultTypes,
                $this->order,
                $this->start,
                $this->limit
            );
        }
        return $this->queryResult;
    }
}