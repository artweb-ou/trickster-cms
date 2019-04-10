<?php

class productCatalogueDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->title;
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