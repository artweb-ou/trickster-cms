<?php

return [
    \controller::class => static function () {
        return controller::getInstance();
    },
    \ConfigManager::class => static function (controller $controller) {
        return $controller->getConfigManager();
    },
];
