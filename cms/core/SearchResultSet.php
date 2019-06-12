<?php

class SearchResultSet
{
    public $type = '';
    public $template = false;
    public $partial = false;
    protected $totalCount = 0;
    public $elements = [];

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    public function getSubCount()
    {
        return false;
    }
}


