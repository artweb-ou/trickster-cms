<?php

class newsMailAddressDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->email;
            $info['personalName'] = $element->personalName;
            $info['url'] = $element->URL;
            $info['email'] = $element->email;
            $result[] = $info;
        }
        return $result;
    }
}