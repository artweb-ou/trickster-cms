<?php

class discountDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            $info['url'] = $element->URL;
            $info['image'] = $element->image;
            $info['icon'] = $element->icon;
            $info['link'] = $element->link;
            $info['content'] = $element->content;
            $result[] = $info;
        }

        return $result;
    }
}

