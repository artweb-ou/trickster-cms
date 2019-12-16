<?php

class smartPostImportManager extends errorLogger
{
    const RESOURCE_URL_EST = 'http://www.smartpost.ee/places.js';
    const RESOURCE_URL_FIN = 'http://smartpost.ee/fi_apt.json';

    public function getPostAutomates()
    {
        return $this->fetchInfo();
    }

    protected function fetchInfo()
    {
        $data = [];
        if (ini_get('allow_url_fopen')) {
            if ($text = file_get_contents(self::RESOURCE_URL_EST)) {
                $text = substr($text, strlen('var places_info = '));
                $text = substr($text, 0, -2);
                if ($dataEstSource = json_decode($text, true)) {
                    foreach ($dataEstSource as $key => $item) {
                        if (!empty($item['active'])) {
                            $data[] = [
                                'place_id' => $item['place_id'],
                                'group_id' => $item['group_id'],
                                'group_name' => $item['group_name'],
                                'group_sort' => $item['group_sort'],
                                'name' => $item['name'],
                                'address' => $item['address'],
                                'city' => mb_convert_case($item['city'], MB_CASE_TITLE),
                                'country' => 'ee',
                            ];
                        }
                    }
                }

            }
            if ($text = file_get_contents(self::RESOURCE_URL_FIN)) {
                if ($dataFinSource = json_decode($text, true)) {
                    foreach ($dataFinSource as $key => $item) {
                        $data[] = [
                            'place_id' => $item['code'],
                            'group_id' => $item['city'],
                            'group_name' => mb_convert_case($item['city'], MB_CASE_TITLE),
                            'group_sort' => $key,
                            'name' => $item['postoffice'],
                            'address' => $item['address'],
                            'city' => mb_convert_case($item['city'], MB_CASE_TITLE),
                            'opened' => $item['availability'],
                            'country' => 'fi',
                        ];
                    }
                }
            }
        } else {
            $this->logError('Download failure - allow_url_fopen=false');
        }
        return "var places_info = " . json_encode($data) . ";";
    }
}