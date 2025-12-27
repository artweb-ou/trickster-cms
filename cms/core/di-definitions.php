<?php

use App\Logging\RedisRequestLogger;
use Illuminate\Database\Connection;
use function DI\factory;
use function DI\get;

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

];
