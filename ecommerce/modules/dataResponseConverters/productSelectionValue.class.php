<?php

class productSelectionValueDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            if ($selectionElement = $element->getSelectionElement()) {
                $info['title'] .= " (" . $selectionElement->title . ")";
            }
            $result[] = $info;
        }
        return $result;
    }
}