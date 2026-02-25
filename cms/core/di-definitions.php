<?php
declare(strict_types=1);

use App\Logging\EventsLog;
use App\Logging\RedisRequestLogger;
use App\Paths\PathsManager;
use App\Structure\ActionFactory;
use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use function DI\autowire;
use function DI\factory;

$loadDb = static function (ConfigManager $configManager, string $config): ?Connection {
    $dbConfig = $configManager->getConfig($config);
    if ($dbConfig === null || $dbConfig->isEmpty()) {
        return null;
    }
    $capsule = new Capsule();
    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => $dbConfig->get('mysqlHost'),
        'database' => $dbConfig->get('mysqlDatabase'),
        'username' => $dbConfig->get('mysqlUser'),
        'password' => $dbConfig->get('mysqlPassword'),
        'charset' => $dbConfig->get('mysqlConnectionEncoding'),
        'collation' => 'utf8mb4_bin',
        'prefix' => $dbConfig->get('mysqlTablesPrefix'),
        'options' => [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_TIMEOUT => 1,
        ],
    ]);
    $capsule->setFetchMode(PDO::FETCH_ASSOC);
    if ($config === 'transport') {
        $capsule->setAsGlobal();
    }
    $connection = $capsule->getConnection();
    if ($pdo = $connection->getPdo()) {
        $pdo->query('SET sql_mode = ""');
    }
    return $connection;
};

return [
    controller::class => static function () {
        return controller::getInstance();
    },
    ConfigManager::class => static function (controller $controller) {
        return $controller->getConfigManager();
    },
    PathsManager::class => static function (controller $controller) {
        return $controller->getPathsManager();
    },
    renderer::class => static fn() => renderer::getInstance(),

    LanguagesManager::class => factory(static function (Container $container, ServerSessionManager $ssm) {
        $lm = new LanguagesManager();
        $lm->setContainer($container);
        $lm->setServerSessionManager($ssm);
        return $lm;
    }),

    breadcrumbsManager::class => factory(static function (Container $container, ConfigManager $configManager) {
        $bm = new breadcrumbsManager();
        $bm->setContainer($container);
        if ($config = $configManager->getConfig('breadcrumbs')) {
            $bm->setConfig($config);
        }
        return $bm;
    }),

    translationsManager::class => factory(static function (Container $container, ConfigManager $configManager) {
        $tm = new translationsManager();
        $tm->setContainer($container);
        $tm->enableLogging($configManager->get('main.logMissingTranslations'));
        return $tm;
    }),

    Connection::class => factory(static function (ConfigManager $configManager) use ($loadDb) {
        return $loadDb($configManager, 'transport');
    }),
    'db' => DI\get(Connection::class),

    'statsDb' => factory(static function (ConfigManager $configManager, Connection $db) use ($loadDb) {
        return $loadDb($configManager, 'statstransport') ?? $db;
    }),

    DesignThemesManager::class => factory(static function (
        ConfigManager        $configManager,
        controller           $controller,
        ServerSessionManager $ssm,
    ) {
        $dtm = new DesignThemesManager();
        $themes = $configManager->get('paths.themes');
        foreach ($controller->getIncludePaths() as $path) {
            $dtm->setThemesDirectoryPath($path . $themes);
        }
        $dtm->setServerSessionManager($ssm);
        $dtm->setCurrentThemeCode($configManager->get('main.publicTheme'));
        return $dtm;
    }),

    EmailDispatcher::class => factory(static function (DesignThemesManager $dtm, ConfigManager $configManager) {
        $ed = new EmailDispatcher();
        $ed->setDesignThemesManager($dtm);
        $timeLimit = $configManager->get('emails.timeLimit');
        if ($timeLimit !== null && $timeLimit !== false) {
            $ed->setTimeLimit($timeLimit);
        }
        return $ed;
    }),

    ResourcesUniterHelper::class => factory(static function (
        DesignThemesManager  $dtm,
        requestHeadersManager $rhm,
        PathsManager         $pathsManager,
    ) {
        $ruh = new ResourcesUniterHelper();
        $ruh->setDesignThemesManager($dtm);
        if ($userAgentEngineType = $rhm->getUserAgentEngineType()) {
            $ruh->setUserAgentEngineType($userAgentEngineType);
        }
        if ($userAgent = $rhm->getUserAgent()) {
            $ruh->setUserAgent($userAgent);
        }
        if ($userAgentVersion = $rhm->getUserAgentVersion()) {
            $ruh->setUserAgentVersion($userAgentVersion);
        }
        $ruh->setCssCachePath($pathsManager->getPath('cssCache'));
        $ruh->setJsCachePath($pathsManager->getPath('javascriptCache'));
        return $ruh;
    }),

    DeploymentManager::class => factory(static function (PathsManager $pathsManager) {
        $dm = new DeploymentManager();
        $dm->setDirectory(ROOT_PATH . '../deployments/');
        if ($path = $pathsManager->getPath('newDeployments')) {
            $dm->setIncomingDirectory($path);
        }
        return $dm;
    }),

    UpdatesApi::class => factory(static function (ConfigManager $configManager, PathsManager $pathsManager) {
        $ua = new UpdatesApi();
        $ua->setApiUrl($configManager->get('main.updatesUrl'));
        $ua->setLicenceKey($configManager->get('main.licenceKey'));
        $ua->setLicenceName($configManager->get('main.licenceName'));
        $path = $pathsManager->getPath('newDeployments');
        $pathsManager->ensureDirectory($path);
        $ua->setWorkspaceDir($path);
        return $ua;
    }),

    structureManager::class => DI\get('publicStructureManager'),

    'publicStructureManager' => factory(static function (
        Container        $container,
        ActionFactory    $actionFactory,
        linksManager     $linksManager,
        LanguagesManager $languagesManager,
        privilegesManager $privilegesManager,
        Cache            $cache,
        ConfigManager    $configManager,
        controller       $controller,
    ) {
        $sm = new structureManager();
        $sm->setContainer($container);
        $sm->setActionFactory($actionFactory);
        $sm->setLinksManager($linksManager);
        $sm->setPrivilegesManager($privilegesManager);
        $sm->setLanguagesManager($languagesManager);
        $sm->setRootUrl($controller->rootURL);
        $sm->setRootElementMarker($configManager->get('main.rootMarkerPublic'));
        $sm->setRequestedPath($controller->requestedPath);
        $sm->setPathSearchAllowedLinks($configManager->getMerged('structurelinks.publicAllowed'));
        $sm->setElementPathRestrictionId($languagesManager->getCurrentLanguageId());
        $sm->setCache($cache);

        $deniedCopyLinkTypes = [];
        if ($config = $configManager->getConfig('deniedCopyLinkTypes')) {
            $data = $config->getLinkedData();
            $deniedCopyLinkTypes = array_keys(array_filter($data));
        }
        if ($deniedCopyLinkTypes) {
            $sm->setDeniedCopyLinkTypes($deniedCopyLinkTypes);
        }
        return $sm;
    }),

    'adminStructureManager' => factory(static function (
        Container        $container,
        ActionFactory    $actionFactory,
        linksManager     $linksManager,
        LanguagesManager $languagesManager,
        privilegesManager $privilegesManager,
        Cache            $cache,
        ConfigManager    $configManager,
        controller       $controller,
    ) {
        $sm = new structureManager();
        $sm->setContainer($container);
        $sm->setActionFactory($actionFactory);
        $sm->setLinksManager($linksManager);
        $sm->setLanguagesManager($languagesManager);
        $sm->setPrivilegesManager($privilegesManager);
        $sm->setRootUrl($controller->rootURL);
        $sm->setRequestedPath($controller->requestedPath);
        $sm->setRootElementMarker($configManager->get('main.rootMarkerAdmin'));
        $sm->setPathSearchAllowedLinks($configManager->getMerged('structurelinks.adminAllowed'));
        $sm->setCache($cache);
        $sm->defaultActions = $configManager->getConfig('actions')->getLinkedData();

        $deniedCopyLinkTypes = [];
        if ($config = $configManager->getConfig('deniedCopyLinkTypes')) {
            $data = $config->getLinkedData();
            $deniedCopyLinkTypes = array_keys(array_filter($data));
        }
        if ($deniedCopyLinkTypes) {
            $sm->setDeniedCopyLinkTypes($deniedCopyLinkTypes);
        }
        $container->set(structureManager::class, $sm);
        return $sm;
    }),

    adminApplication::class => autowire()
        ->method('setService', 'structureManager', DI\get('adminStructureManager')),
    adminAjaxApplication::class => autowire()
        ->method('setService', 'structureManager', DI\get('adminStructureManager')),
    exportApplication::class => autowire()
        ->method('setService', 'structureManager', DI\get('adminStructureManager')),
    VisitorRemoveApplication::class => autowire()
        ->method('setService', 'structureManager', DI\get('adminStructureManager')),

    EventsLog::class => autowire()
        ->constructorParameter('statsDb', DI\get('statsDb'))
        ->constructorParameter('db', DI\get(Connection::class)),

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

    // DependencyInjectionContextTrait users created as PHP-DI factory dependencies
    privilegesManager::class => autowire()->method('setContainer', DI\get(Container::class)),
    ActionFactory::class => autowire()->method('setContainer', DI\get(Container::class)),

];
