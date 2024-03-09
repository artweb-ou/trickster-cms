<?php

class RedisServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return new Redis();
    }

    public function makeInjections($instance)
    {
        /**
         * @var ConfigManager $configManager
         */
        $configManager = $this->registry->getService('ConfigManager');
        if ($redisConfig = $configManager->getConfig('redis')) {
            $instance->connect($redisConfig->get('host'), $redisConfig->get('port'), $redisConfig->get('connectionTimeout'));
            $instance->auth($redisConfig->get('pass'));
        }
        return $instance;
    }
}