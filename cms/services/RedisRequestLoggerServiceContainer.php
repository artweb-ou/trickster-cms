<?php

use App\Logging\RedisRequestLogger;

class RedisRequestLoggerServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance(): RedisRequestLogger
    {
        $redis = $this->registry->getService('Redis');
        return new RedisRequestLogger($redis, 60 * 35);
    }

    public function makeInjections($instance)
    {
    }
}