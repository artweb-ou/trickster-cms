<?php
declare(strict_types=1);

use App\Logging\RedisRequestLogger;
use Illuminate\Database\Connection;
use function DI\factory;

return [
    controller::class => static function () {
        return controller::getInstance();
    },
    ConfigManager::class => static function (controller $controller) {
        return $controller->getConfigManager();
    },
    PathsManager::class => static function (controller $controller) {
        return $controller->getApplication()->getService('PathsManager');
    },
    LanguagesManager::class => static function (controller $controller) {
        return $controller->getApplication()->getService('LanguagesManager');
    },
    structureManager::class => static function (controller $controller) {
        return $controller->getApplication()->getService('structureManager');
    },
    Connection::class => static function (controller $controller) {
        return $controller->getApplication()->getService('db');
    },
    RedisRequestLogger::class => factory(
        fn(
            ConfigManager $cm,
            Redis         $redis
        ) => new RedisRequestLogger(
            $cm->getConfig('redis')->get('enabled'),
            $redis,
            600
        )
    ),
    Redis::class => factory(
        function (
            ConfigManager $configManager,
        ) {
            $instance = new Redis();
            if ($redisConfig = $configManager->getConfig('redis')) {
                $instance->connect($redisConfig->get('host'), $redisConfig->get('port'), $redisConfig->get('connectionTimeout'));
                $instance->auth($redisConfig->get('pass'));
            }
            return $instance;
        }
    ),

];
