<?php

class formFieldDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['structureType'] = $element->structureType;
            $info['structurePath'] = $element->structurePath;
            $info['title'] = $element->title;
            $info['url'] = $element->URL;
            $result[] = $info;
        }

        return $result;
    }
}