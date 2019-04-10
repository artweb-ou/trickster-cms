<?php

class productionDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
            $info['url'] = $element->URL;
            $info['introduction'] = $element->image;
            $info['link'] = $element->link;
            $info['content'] = $element->content;
            if ($relatedLanguage = $element->getRelatedLanguageElement()) {
                $info['language'] = $relatedLanguage->iso6393;
            } else {
                $info['language'] = "";
            }
            $result[] = $info;
        }
        return $result;
    }
}

