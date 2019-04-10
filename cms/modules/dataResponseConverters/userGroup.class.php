<?php

class userGroupDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->description;
            $result[] = $info;
        }
        return $result;
    }
}