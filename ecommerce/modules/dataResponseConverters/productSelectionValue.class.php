<?php

class productSelectionValueDataResponseConverter extends dataResponseConverter
{
    /**
     * @param structureElement[] $data
     * @return array
     */
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            if ($selectionElement = $element->getSelectionElement()) {
                $info['title'] .= " (" . $selectionElement->getTitle() . ")";
            }
            $result[] = $info;
        }
        return $result;
    }
}