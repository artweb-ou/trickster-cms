<?php

class productSelectionDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            if ($parameterGroup = $element->getParameterGroup()) {
                $info['title'] .= " (" . $parameterGroup->title . ")";
            }
            $result[] = $info;
        }
        return $result;
    }
}