<?php

class shopDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['structureType'] = $element->structureType;
            $info['structurePath'] = $element->structurePath;
            $info['title'] = $element->title;
            $info['url'] = $element->URL;
            $info['introduction'] = $element->introduction;
            $info['content'] = $element->content;
            $info['introductionText'] = $this->htmlToPlainText($element->introduction);
            $info['contentText'] = $this->htmlToPlainText($element->content);
            $info['image'] = $element->image;
            $result[] = $info;
        }
        return $result;
    }
}