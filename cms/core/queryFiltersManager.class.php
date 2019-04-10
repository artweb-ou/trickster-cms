<?php

class queryFiltersManager extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    /** @var queryFiltersManager */
    protected static $instance = false;

    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * @return queryFiltersManager
     * @deprecated
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $className = __CLASS__;
            self::$instance = new $className();
        }
        return self::$instance;
    }

    /**
     * @param $filterName
     * @return bool|queryFilter
     */
    protected function getFilter($filterName)
    {
        $filter = false;
        $className = $filterName . 'QueryFilter';
        if (!class_exists($className, false)) {
            $fileName = $filterName . '.class.php';
            $pathsManager = $this->getService('PathsManager');
            $fileDirectory = $pathsManager->getRelativePath('queryFilters');
            if ($filePath = $pathsManager->getIncludeFilePath($fileDirectory . $fileName)) {
                include_once($filePath);
            }
        }
        if (class_exists($className, false)) {
            $filter = new $className();
            if ($filter instanceof DependencyInjectionContextInterface) {
                $this->instantiateContext($filter);
            }
        } else {
            $this->logError('queryFilter class "' . $className . '" is missing');
        }
        return $filter;
    }

    /**
     * @param $type
     * @return bool|queryFilterConverter
     */
    public function getConverter($type)
    {
        $converter = false;
        $className = $type . 'QueryFilterConverter';
        if (!class_exists($className, false)) {
            $fileName = $type . 'QueryFilterConverter.class.php';
            $pathsManager = $this->getService('PathsManager');
            $fileDirectory = $pathsManager->getRelativePath('queryFilterConverters');
            if ($filePath = $pathsManager->getIncludeFilePath($fileDirectory . $fileName)) {
                include_once($filePath);
            }
        }
        if (class_exists($className, false)) {
            $converter = new $className();
            if ($converter instanceof DependencyInjectionContextInterface) {
                $this->instantiateContext($converter);
            }
            $converter->setType($type);
        } else {
            $this->logError('queryFilterConverter class "' . $className . '" is missing');
        }
        return $converter;
    }

    /**
     * @param array $parameters - Filter types
     * @param array $resultTypes - Object types
     * @param bool $optimized - enable parameters sorting top optimized result query
     * @return array|bool
     */
    public function getFilterIdLists($parameters, $resultTypes, $optimized = true)
    {
        $result = false;
        if ($parameters) {
            $finalResultsIndex = $this->compileResultsIndex($resultTypes);
            foreach ($parameters as $parametersList) {
                if ($groupResults = $this->getFilterGroupResults($parametersList, $resultTypes, $optimized)) {
                    foreach ($finalResultsIndex as $type => $value) {
                        if ($groupResults[$type] && $records = $groupResults[$type]->get()) {
                            $finalResultsIndex[$type] = array_merge($finalResultsIndex[$type], array_column($records, 'id'));
                        }
                    }
                }
            }
            $result = $finalResultsIndex;
        }
        return $result;
    }

    /**
     * @param array $parametersList IDs list from one filter
     * @param array $resultTypes Object types
     * @param $optimized - sort the parameters to get less type conversions and simplier query
     * @return array|bool
     */
    protected function getFilterGroupResults($parametersList, $resultTypes, $optimized)
    {
        $resultNotFound = false;

        if ($optimized) {
            $parametersList = $this->optimizeParametersList($parametersList, $resultTypes);
        }

        $groupResults = $this->compileResultsIndex($resultTypes);
        $filterResult = false;
        $cachedResults = [];
        $previousType = false;
        foreach ($parametersList as $filterName => $filterArgument) {
            if ($filter = $this->getFilter($filterName)) {
                $incomingType = $filter->getRequiredType();
                if ($nextFilterData = $this->compileNextFilterData($filterResult, $incomingType, $previousType, $cachedResults)) {
                    $cachedResults[$incomingType] = $nextFilterData;
                }
                if ($filterResult = $filter->getFilteredIdList($filterArgument, $nextFilterData)) {
                    $cachedResults[$incomingType] = $filterResult;
                } else {
                    $resultNotFound = true;
                    break;
                }
                $previousType = $incomingType;
            }
        }
        if (!$resultNotFound) {
            foreach ($resultTypes as &$type) {
                if ($type == $previousType) {
                    // use filter's query results for this type
                    $groupResults[$type] = $filterResult;
                } else {
                    // filtration did not query this type, perform a secondary filtration
                    // using the results from main filtration
                    $groupResults[$type] = $this->convertTypeData($filterResult, $type, $previousType, $cachedResults);
                }
            }
        }
        return $groupResults;
    }

    public function optimizeParametersList($parametersList, $resultTypes)
    {
        $typesIndex = array_flip($resultTypes);

        $parametersInfo = [];
        foreach ($parametersList as $type => $value) {
            $parametersInfo[] = [$type, $value];
        }

        usort($parametersInfo, function ($a, $b) use ($typesIndex) {
            if (($filter1 = $this->getFilter($a[0])) && ($filter2 = $this->getFilter($b[0]))) {
                $type1 = $filter1->getRequiredType();
                $type2 = $filter2->getRequiredType();
                if (isset($typesIndex[$type1])) {
                    if (isset($typesIndex[$type2])) {
                        return strcmp($type1, $type2);
                    }
                    return 1;
                } elseif (isset($typesIndex[$type2])) {
                    return -1;
                }
                return strcmp($type1, $type2);
            }
            return 0;
        }
        );

        $parametersList = [];
        foreach ($parametersInfo as $info) {
            $parametersList[$info[0]] = $info[1];
        }

        return $parametersList;
    }

    /**
     * @param Illuminate\Database\Query\Builder|array $sourceData
     * @param string $targetType
     * @param string $sourceType
     * @param Illuminate\Database\Query\Builder[]|[][] $cachedResults
     * @return Illuminate\Database\Query\Builder
     */
    public function convertTypeData($sourceData, $targetType, $sourceType, $cachedResults)
    {
        $convertedData = false;
        if ($converter = $this->getConverter($targetType)) {
            if (isset($cachedResults[$targetType])) {
                $converter->setCorrectionQuery($cachedResults[$targetType]);
            }
            $convertedData = $converter->convert($sourceData, $sourceType);
        }
        return $convertedData;
    }

    protected function compileNextFilterData($filterResult, $incomingType, $previousType, $cachedResults)
    {
        $nextFilterData = false;
        if (!$incomingType || $previousType == $incomingType) {
            $nextFilterData = $filterResult;
        } elseif ($incomingType) {
            $nextFilterData = $this->convertTypeData($filterResult, $incomingType, $previousType, $cachedResults);
        }
        return $nextFilterData;
    }

    protected function compileResultsIndex($resultTypes)
    {
        $finalResultsList = [];
        foreach ($resultTypes as &$type) {
            $finalResultsList[$type] = [];
        }
        return $finalResultsList;
    }
}

abstract class queryFilter extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    /**
     * return type of query converter required to provide the right query for the filter
     *
     * @return string|boolean
     */
    abstract public function getRequiredType();

    /**
     * @param mixed $argument
     * @param Illuminate\Database\Query\Builder $query
     * @return mixed
     */
    abstract public function getFilteredIdList($argument, $query);
}

abstract class queryFilterConverter extends errorLogger implements DependencyInjectionContextInterface
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