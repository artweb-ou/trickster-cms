<?php

class productParameterDataResponseConverter extends dataResponseConverter
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
            if ($parameterGroup = $element->getParameterGroup()) {
                $info['title'] .= " (" . $parameterGroup->getTitle() . ")";
            }
            $result[] = $info;
        }
        return $result;
    }
}