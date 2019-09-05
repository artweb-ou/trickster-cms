<?php

class facebookSocialPluginElement extends socialPluginElement
{
    protected $allowedTypes = [
        'socialPage',
        'instagramImage'
    ];
    protected $pages;

    public function getSpecialFields()
    {
        return [
            'appId'  => [
                'format'        => 'text',
                'multiLanguage' => false,
            ],
            'appKey' => [
                'format'        => 'text',
                'multiLanguage' => false,
            ],
        ];
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showSocialPages',
            'showInstagramImages',
        ];
    }

    public function getApiClass()
    {
        return 'facebookSocialNetworkAdapter';
    }

    public function getPages()
    {
        if (is_null($this->pages)) {
            $return = [];
            foreach ($this->getChildrenList() as $child) {
                if ($child->structureType == 'socialPage') {
                    $return[] = $child;
                }
            }
            $this->pages = $return;
        }
        return $this->pages;
    }

    public function getInstagramImages($pageSocialId = false)
    {
        $return = [];
        foreach ($this->getChildrenList() as $child) {
            if ($child->structureType == 'instagramImage') {
                if(!$pageSocialId || $child->pageSocialId == $pageSocialId) {
                    $return[] = $child;
                }
            }
        }
        return $return;
    }

    public function getPageBySocialId($socialId)
    {
        foreach($this->getPages() as $page) {
            if($page->socialId == $socialId) {
                return $page;
            }
        }

        return false;
    }
}