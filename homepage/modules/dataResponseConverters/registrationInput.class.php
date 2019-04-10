<?php

class registrationInputDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title . '(' . $element->id . ')';
            $result[] = $info;
        }
        return $result;
    }
}