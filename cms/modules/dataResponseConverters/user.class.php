<?php

class userDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        $result = [];
        foreach ($data as &$element) {
            $info = [];
            $info['id'] = $element->id;
            $info['title'] = $element->userName;
            $info['userName'] = $element->userName;
            $info['firstName'] = $element->firstName;
            $info['lastName'] = $element->lastName;
            $info['email'] = $element->email;
            $info['phone'] = $element->phone;
            $info['website'] = $element->website;
            $result[] = $info;
        }
        return $result;
    }
}