<?php

class userGroupQueryFilterConverter extends queryFilterConverter
{
    public function convert($sourceData, $sourceType)
    {
        $query = $this->getService('db')->table('module_user_group')->select('id');
        return $query;
    }
}