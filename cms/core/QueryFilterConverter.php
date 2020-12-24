<?php

abstract class QueryFilterConverter extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    /**
     * @var Illuminate\Database\Query\Builder
     */
    protected $correctionQuery;

    /**
     * @return Illuminate\Database\Query\Builder
     */
    public function getCorrectionQuery()
    {
        return $this->correctionQuery;
    }

    /**
     * @param Illuminate\Database\Query\Builder $correctionQuery
     */
    public function setCorrectionQuery($correctionQuery)
    {
        $this->correctionQuery = $correctionQuery;
    }

    protected $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param Illuminate\Database\Query\Builder $sourceData
     * @param string $sourceType
     * @return mixed
     */
    abstract public function convert($sourceData, $sourceType);
}