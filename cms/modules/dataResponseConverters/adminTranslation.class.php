<?php

class adminTranslationDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['structureType'] = $element->structureType;
            $info['title'] = $element->structureName;
            $info['url'] = $element->URL;
            $result[] = $info;
        }

        return $result;
    }
}