<?php

class SocialDataManager extends errorLogger
    implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $socialPlugins;

    public function getCmsUserId($socialNetwork, $socialId)
    {
        return (int)$this->queryUsers()
            ->select(['userId'])
            ->where('socialNetwork', '=', $socialNetwork)
            ->where('userSocialId', '=', $socialId)
            ->value('userId');
    }

    public function addSocialUser($socialNetwork, $socialId, $userId)
    {
        $this->queryUsers()
            ->insert([
                'socialNetwork' => $socialNetwork,
                'userSocialId' => $socialId,
                'userId' => $userId,
            ]);
    }

    public function removeSocialUser($userId, $socialNetwork = '')
    {
        $conditions = ['userId' => $userId];
        if ($socialNetwork !== '') {
            $conditions['socialNetwork'] = $socialNetwork;
        }
        return $this->queryUsers()
            ->where($conditions)
            ->delete();
    }

    public function getCmsUserSocialNetworks($userId)
    {
        return (array)$this->queryUsers()
            ->where('userId', '=', $userId)
            ->lists('socialNetwork');
    }

    public function getSocialPlugins()
    {
        if ($this->socialPlugins === null) {
            $this->socialPlugins = [];
            $structureManager = $this->getService('structureManager');
            $socialPluginsElement = null;
            $socialPluginsElementId = $structureManager->getElementIdByMarker('socialPlugins');
            if ($socialPluginsElementId) {
                $linksManager = $this->getService('linksManager');
                $structureManager->getRootElement();
                if ($socialPluginsIds = $linksManager->getConnectedIdList($socialPluginsElementId, 'structure', 'parent')) {
                    $this->socialPlugins = $structureManager->getElementsByIdList($socialPluginsIds, null, true);
                }
            }
        }
        return $this->socialPlugins;
    }

    public function getSocialPluginByName($name)
    {
        static $index;

        if ($index === null) {
            $index = [];
            foreach ($this->getSocialPlugins() as $plugin) {
                $index[$plugin->getName()] = $plugin;
            }
        }
        return isset($index[$name]) ? $index[$name] : null;
    }

    protected function queryUsers()
    {
        return $this->getService('db')->table('social_users');
    }
}