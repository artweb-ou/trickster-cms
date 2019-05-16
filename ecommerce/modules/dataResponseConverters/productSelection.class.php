<?php

class productSelectionDataResponseConverter extends dataResponseConverter
{
    /**
     * @param productSelectionElement[] $data
     * @return array
     */
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            $info['url'] = $element->URL;
            if ($parameterGroup = $element->getParameterGroup()) {
                $info['title'] .= " (" . $parameterGroup->getTitle() . ")";
            }
            $result[] = $info;
        }
        return $result;
    }
}