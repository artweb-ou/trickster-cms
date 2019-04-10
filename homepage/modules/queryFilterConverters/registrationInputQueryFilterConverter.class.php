<?php

class registrationInputQueryFilterConverter extends queryFilterConverter
{
    public function convert($sourceData, $sourceType)
    {
        $query = $this->getService('db')->table('module_form_field')->select('id')->distinct();
        return $query;
    }
}