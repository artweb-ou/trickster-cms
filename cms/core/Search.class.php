<?php

class Search implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $types = [];
    protected $filters = [];
    protected $offset = 0;
    protected $limit = 50;
    protected $partialMatching = false;
    protected $contentMatching = false;
    protected $singlePageCombining = false;
    protected $input = '';

    public function setInput($input)
    {
        $this->input = (string)$input;
    }

    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    public function setOffset($offset)
    {
        $this->offset = (int)$offset;
    }

    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
    }

    public function setPartialMatching($enabled = true)
    {
        $this->partialMatching = (bool)$enabled;
    }

    public function setContentMatching($enabled = true)
    {
        $this->contentMatching = (bool)$enabled;
    }

    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function setSinglePageCombining($enabled = true)
    {
        $this->singlePageCombining = (bool)$enabled;
    }

    public function getResult()
    {
        $result = new SearchResult();
        $this->types = array_unique($this->types);

        $this->performTypeSearch($result);
        $result->exactMatches = count($result->sets);
        if (!$result->exactMatches && $this->partialMatching) {
            $this->performTypeSearch($result, false, '_partial');
        }
        return $result;
    }

    protected function performTypeSearch($searchResult, $exact = true, $typePostfix = '', $exclusions = [])
    {
        $structureManager = $this->getService('structureManager');
        $apiQueriesManager = $this->getService('ApiQueriesManager');
        $idsByType = [];

        if ($exact) {
            $input = trim($this->input);
        } else {
            $input = $this->generateQueryStrings($this->input);
        }

        if ($input) {
            foreach ($this->types as &$type) {
                $apiQuery = $apiQueriesManager->getQuery();
                $apiQuery->setExportType($type);
                $queryParameters = [];
                if ($this->contentMatching) {
                    $queryParameters[$type . 'Search'] = $input;
                } else {
                    $queryParameters[$type . 'AjaxSearch'] = $input;
                }
                $queryParameters += $this->filters;
                if (!empty($exclusions[$type])) {
                    $queryParameters['structureSkipId'] = $exclusions[$type];
                }
                $apiQuery->setFiltrationParameters([$queryParameters]);
                $queryResult = $apiQuery->getFilteredIdLists();
                if (!empty($queryResult[$type])) {
                    $idsByType[$type . $typePostfix] = $queryResult[$type];
                }
            }
            if ($idsByType) {
                if ($this->singlePageCombining) {
                    $averageAmount = floor($this->limit / count($idsByType));
                    $extraAmount = 0;
                    $typesWithExtra = 0;
                    foreach ($idsByType as $type => &$idList) {
                        $typeResultsAmount = count($idList);
                        if ($typeResultsAmount < $averageAmount) {
                            $extraAmount += $averageAmount - $typeResultsAmount;
                        } else {
                            $typesWithExtra++;
                        }
                    }

                    foreach ($idsByType as $type => &$idList) {
                        $set = new SearchResultSet();
                        $set->type = $type;
                        $set->partial = !$exact;

                        $typeResultsAmount = count($idList);
                        if ($typeResultsAmount < $averageAmount) {
                            $sliceAmount = $typeResultsAmount;
                        } else {
                            $sliceAmount = $averageAmount + round($extraAmount / $typesWithExtra);
                        }

                        $idList = array_slice($idList, 0, $sliceAmount);
                        foreach ($idList as $elementId) {
                            if ($element = $structureManager->getElementById($elementId)) {
                                $set->elements[] = $element;
                                $searchResult->elements[] = $element;
                            }
                        }
                        $searchResult->sets[] = $set;
                        $searchResult->count += $sliceAmount;
                    }
                } else {
                    $resultsNeeded = $this->limit;
                    foreach ($idsByType as $type => &$idList) {
                        $queriesResults[$type . $typePostfix] = $idsByType[$type];
                        $typeResultsCount = count($idsByType[$type]);
                        if ($resultsNeeded > 0) {
                            $set = new SearchResultSet();
                            $set->type = $type;
                            $set->partial = !$exact;

                            $i = $searchResult->count;
                            foreach ($idList as $elementId) {
                                if (++$i > $this->offset) {
                                    if ($element = $structureManager->getElementById($elementId)) {
                                        $set->elements[] = $element;
                                        $searchResult->elements[] = $element;
                                    }
                                    --$resultsNeeded;
                                    if ($resultsNeeded == 0) {
                                        break;
                                    }
                                }
                            }
                            $searchResult->sets[] = $set;
                        }
                        $searchResult->count += $typeResultsCount;
                    }
                }
            }
        }
        return $idsByType;
    }

    protected function generateQueryStrings($query)
    {
        $queryStrings = [];
        $words = explode(" ", $query);
        foreach ($words as &$word) {
            $word = trim($word);
            if (mb_strlen(trim($word)) > 2) {
                $queryStrings[] = $word;
            }
        }

        return $queryStrings;
    }
}

class SearchResult
{
    public $count = 0;
    public $sets = [];
    public $elements = [];
    public $exactMatches = true;
}

class SearchResultSet
{
    public $type = '';
    public $template = false;
    public $partial = false;
    public $count = 0;
    public $elements = [];
}


