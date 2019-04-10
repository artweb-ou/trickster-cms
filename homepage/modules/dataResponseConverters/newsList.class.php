<?php

class newsListDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            $info['structureType'] = $element->structureType;
            $info['title'] .= " (" . $element->getParentElementTitle() . ")";

            $result[] = $info;
        }
        return $result;
    }
}