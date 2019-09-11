<?php

class instagramImagesHolderElement extends structureElement
{
    public $dataResourceName = 'module_instagram_images_holder';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['pageSocialId'] = 'text';
    }

    public function getImages($limit = false)
    {
        $socialDataManager = $this->getService('socialDataManager');
        if ($facebookSocialPlugin = $socialDataManager->getSocialPluginByName('facebook')) {
            return $facebookSocialPlugin->getInstagramImages($this->pageSocialId, $limit);
        }

        return false;
    }

    public function getFacebookPages()
    {
        $return = [];
        $socialDataManager = $this->getService('socialDataManager');
        if ($facebookSocialPlugin = $socialDataManager->getSocialPluginByName('facebook')) {
            foreach ($facebookSocialPlugin->getPages() as $page) {
                $return[$page->socialId] = [
                    'id'     => $page->socialId,
                    'title'  => $page->title,
                    'select' => ($this->pageSocialId == $page->socialId)
                ];
            }
        }

        return $return;
    }
}
