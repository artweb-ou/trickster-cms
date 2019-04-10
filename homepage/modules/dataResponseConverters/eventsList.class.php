<?php

class eventsListDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            $result[] = $info;
        }
        return $result;
    }
}

