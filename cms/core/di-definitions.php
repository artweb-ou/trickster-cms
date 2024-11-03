<?php

use Illuminate\Database\Connection;

return [
    \controller::class => static function () {
        return controller::getInstance();
    },
    \ConfigManager::class => static function (controller $controller) {
        return $controller->getConfigManager();
    },
    \PathsManager::class => static function (controller $controller) {
        return $controller->getApplication()->getService('PathsManager');
    },
    \LanguagesManager::class => static function (controller $controller) {
        return $controller->getApplication()->getService('LanguagesManager');
    },
    \structureManager::class => static function (controller $controller) {
        return $controller->getApplication()->getService('structureManager');
    },
    Connection::class => static function (controller $controller) {
        return $controller->getApplication()->getService('db');
    },
];
