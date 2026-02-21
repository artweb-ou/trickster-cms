<?php
declare(strict_types=1);

use App\Logging\EventsLog;
use App\Logging\RedisRequestLogger;
use App\Paths\PathsManager;
use App\Structure\ActionFactory;
use Illuminate\Database\Connection;
use function DI\autowire;
use function DI\factory;

return [
    controller::class => static function () {
        return controller::getInstance();
    },
    privilegesManager::class => static function () {
        return privilegesManager::getInstance();
    },
    ConfigManager::class => static function (controller $controller) {
        return $controller->getConfigManager();
    },
    PathsManager::class => static function (controller $controller) {
        return $controller->getPathsManager();
    },
    LanguagesManager::class => static function (controller $controller) {
        return $controller->getRegistry()->getService('LanguagesManager');
    },
    ActionFactory::class => static function (controller $controller) {
        return $controller->getRegistry()->getService('ActionFactory');
    },
    'publicStructureManager' => factory(static function (
        ActionFactory $actionFactory
    ) {
        $controller = controller::getInstance();
        $configManager = $controller->getConfigManager();
        $languagesManager = $controller->getRegistry()->getService('LanguagesManager');
        $registry = $controller->getRegistry();
        $sm = new structureManager();

        $sm->setRegistry($registry);
        $sm->setContainer($controller->getContainer());
        $sm->setActionFactory($actionFactory);
        $sm->setLinksManager($controller->getRegistry()->getService('linksManager'));
        $sm->setPrivilegesManager($controller->getRegistry()->getService('privilegesManager'));
        $sm->setLanguagesManager($languagesManager);
        $sm->setRootUrl($controller->baseURL);
        $sm->setRootElementMarker($configManager->get('main.rootMarkerPublic'));
        $sm->setRequestedPath($controller->requestedPath);
        $sm->setPathSearchAllowedLinks($configManager->getMerged('structurelinks.publicAllowed'));
        $sm->setElementPathRestrictionId($languagesManager->getCurrentLanguageId());
        $sm->setCache($controller->getRegistry()->getService('Cache'));

        $deniedCopyLinkTypes = [];
        if ($config = $configManager->getConfig('deniedCopyLinkTypes')) {
            $data = $config->getLinkedData();
            $deniedCopyLinkTypes = array_keys(array_filter($data));
        }
        if ($deniedCopyLinkTypes) {
            $sm->setDeniedCopyLinkTypes($deniedCopyLinkTypes);
        }
        $registry->setService('structureManager', $sm);
        return $sm;
    }),
    Connection::class => static function (controller $controller) {
        return $controller->getRegistry()->getService('db');
    },
    EventsLog::class => autowire()
        ->constructorParameter('statsDb', DI\get('statsDb'))
        ->constructorParameter('db', DI\get(Connection::class)),

    'statsDb' => static function (controller $controller) {
        return $controller->getRegistry()->getService('statsDb');
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
                $enabled = $redisConfig->get('enabled') ?? false;
                if ($enabled) {
                    $instance->connect($redisConfig->get('host'), $redisConfig->get('port'), $redisConfig->get('connectionTimeout'));
                    $instance->auth($redisConfig->get('pass'));
                }
            }
            return $instance;
        }
    ),

];
