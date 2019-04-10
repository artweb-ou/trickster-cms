<?php

class eventDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            $info['url'] = $element->URL;
            $info['content'] = $element->description;
            $result[] = $info;
        }
        return $result;
    }
}

