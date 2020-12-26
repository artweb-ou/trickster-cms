<?php

trait LinkedQueryFilterTrait
{
    /**
     * @param \Illuminate\Database\Query\Builder|array $sourceQuery
     * @param string $table
     * @param string $linkType
     * @param bool $distinct
     * @return \Illuminate\Database\Query\Builder
     */
    protected function generateParentQuery($sourceQuery, $table, $linkType, $distinct = false)
    {
        $sourceQuery->select('id');
        $correctionQuery = $this->getCorrectionQuery();
        $query = $this->getService('db')
            ->table($table)
            ->select($this->fields)
            ->whereIn(
                'id',
                function ($subQuery) use (
                    $sourceQuery,
                    $linkType,
                    $correctionQuery
                ) {
                    if ($sourceQuery instanceof \Illuminate\Database\Query\Builder) {
                        $subQuery->select('parentStructureId')
                            ->from('structure_links')
                            ->where('type', '=', $linkType)
                            ->whereRaw('childStructureId in (' . $sourceQuery->toSql() . ')')
                            ->mergeBindings($sourceQuery);
                    } elseif (is_array($sourceQuery)) {
                        $subQuery->select('parentStructureId')
                            ->from('structure_links')
                            ->where('type', '=', $linkType)
                            ->whereIn('childStructureId', $sourceQuery);
                    }
                    if ($correctionQuery instanceof \Illuminate\Database\Query\Builder) {
                        $subQuery->whereRaw('parentStructureId in (' . $correctionQuery->toSql() . ')')
                            ->mergeBindings($correctionQuery);
                    } elseif (is_array($correctionQuery)) {
                        $subQuery->whereIn('parentStructureId', $correctionQuery);
                    }
                }
            );
        if ($distinct) {
            $query->distinct();
        }
        return $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder|array $sourceQuery
     * @param string $table
     * @param string $linkType
     * @param bool $distinct
     * @return \Illuminate\Database\Query\Builder
     */
    protected function generateChildQuery($sourceQuery, $table, $linkType, $distinct = false)
    {
        $sourceQuery->select('id');

        $correctionQuery = $this->getCorrectionQuery();
        $query = $this->getService('db')
            ->table($table)
            ->select($this->fields)
            ->whereIn(
                'id',
                function ($subQuery) use (
                    $sourceQuery,
                    $linkType,
                    $correctionQuery
                ) {
                    if ($sourceQuery instanceof \Illuminate\Database\Query\Builder) {
                        $subQuery->select('childStructureId')
                            ->from('structure_links')
                            ->where('type', '=', $linkType)
                            ->whereRaw('parentStructureId in (' . $sourceQuery->toSql() . ')')
                            ->mergeBindings($sourceQuery);
                    } elseif (is_array($sourceQuery)) {
                        $subQuery->select('childStructureId')
                            ->from('structure_links')
                            ->where('type', '=', $linkType)
                            ->whereIn('parentStructureId', $sourceQuery);
                    }
                    if ($correctionQuery instanceof \Illuminate\Database\Query\Builder) {
                        $subQuery->whereRaw('childStructureId in (' . $correctionQuery->toSql() . ')')
                            ->mergeBindings($correctionQuery);
                    } elseif (is_array($correctionQuery)) {
                        $subQuery->whereIn('childStructureId', $correctionQuery);
                    }
                }
            );
        if ($distinct) {
            $query->distinct();
        }
        return $query;
    }


    abstract function getCorrectionQuery();
}