<?php

class installerApplication extends controllerApplication
{
    protected $applicationName = 'installer';
    public $rendererName = 'smarty';

    const PROGRESS_NONE = 0;
    const PROGRESS_DOWNLOADED = 1;
    const PROGRESS_INSTALLED = 2;
    /**
     * @var designTheme
     */
    protected $theme;
    /**
     * @var DeploymentManager
     */
    protected $deploymentManager;
    /**
     * @var controller
     */
    protected $controller;
    /**
     * @var structureManager
     */
    protected $structureManager;
    protected static $formDefaults = [
        'db_user' => 'root',
        'db_password' => 'test',
        'db_database' => '',
        'db_host' => '127.0.0.1',
        'db_tables_prefix' => 'engine_',
        'plugin' => ['cms' => 1]
    ];
    protected static $expectedFields = [
        'db_user',
        'db_password',
        'db_database',
        'db_host',
        'db_tables_prefix',
        //'licence_name',
        'licence_key',
        'plugin',
    ];
    protected $action = '';
    protected $installed = '';

    public function initialize()
    {
	ini_set('opcache.enable', 0);
        set_time_limit(60 * 60);
        $this->createRenderer();
        $this->deploymentManager = $this->getService('DeploymentManager');
        $this->structureManager = $this->getService('structureManager');
        $this->structureManager->setPrivilegeChecking(false);
        $this->controller = $this->getService('controller');
    }

    public function execute($controller)
    {
        $action = 'index';
        foreach ($controller->requestedPath as $action) {
            break;
        }
        $configManager = $this->getService('ConfigManager');
        $installProgress = $configManager->get('main.installProgress');
        if ($installProgress === self::PROGRESS_INSTALLED) {
            $action = 'index';
        } elseif ($installProgress === self::PROGRESS_DOWNLOADED) {
            $action = 'install';
        }
        $this->renderer->assign('installProgress', $installProgress);
        if (method_exists($this, 'action' . $action)) {
            $this->action = $action;
            $this->renderer->assign('action', $action);
            call_user_func([$this, 'action' . $action]);
        } else {
            $this->renderer->fileNotFound();
        }
    }

    protected function actionIndex()
    {
        $this->setupTheme();
        $this->renderer->assign('controller', $this->controller);
        $this->renderer->assign('theme', $this->theme);
        $this->renderer->assign('deploymentManager', $this->deploymentManager);
        $this->renderer->assign('plugins', [
            'cms',
            'cms_db',
            'homepage',
            'homepage_db',
            'ecommerce',
            'ecommerce_db',
            'mall',
            'mall_db',
            'mall_demo_db',
            'standardDesign',
            'test',
        ]);
        $this->renderer->assign('dbFields', [
            'db_user',
            'db_password',
            'db_database',
            'db_host',
            'db_tables_prefix',
        ]);
        $this->renderer->assign('licenceFields', [
            //'licence_name',
            'licence_key',
        ]);
        if ($this->action === 'index') {
            $input = [];
            $defaults = self::$formDefaults;
            foreach (self::$expectedFields as $field) {
                if (isset($defaults[$field])) {
                    $input[$field] = $defaults[$field];
                } else {
                    $input[$field] = '';
                }
            }
            $this->renderer->assign('input', $input);
        }
        $this->renderer->setCacheControl('no-cache');
        $this->renderer->template = $this->theme->template('index.tpl');
        $this->renderer->display();
    }

    protected function actionInstall()
    {
        //setup it early to avoid interfaces incompatibility problems
        $this->setupTheme();
        $configManager = $this->getService('ConfigManager');

        $projectConfig = $configManager->getConfigFromPath(ROOT_PATH . 'project/config/main.php');
        $mainConfig = $configManager->getConfigFromPath(ROOT_PATH . 'trickster/cms/config/main.php');
        $deploymentsManager = $this->getService('DeploymentManager');
        $installProgress = (int)$mainConfig->get('installProgress');
        $error = '';
        $input = isset($_POST) ? $_POST : [];

        // generate configs, download packages
        if (!is_dir(ROOT_PATH . 'project/config/dev/')) {
            mkdir(ROOT_PATH . 'project/config/dev/', 0777, true);
        }
        foreach (self::$expectedFields as $field) {
            if (!isset($input[$field])) {
                $input[$field] = '';
            }
            if ($input[$field] === '') {
                $error = 'Make sure the form is filled!';
            }
        }
        if ($error === '') {
            $this->handleDb($input, $error);
        }
        if ($error === '') {
            $this->installComposerPackages($error);
        }
        if ($error === '') {
            $plugins = array_keys((array)$input['plugin']);
            $pluginsSettings = [];
            $pluginsSettings['cms'] = 'trickster/cms/';
            foreach ($plugins as $plugin) {
                if (strpos($plugin, '_db') !== false) {
                    continue;
                }
                $pluginsSettings[$plugin] = "trickster/$plugin/";
            }
            $pluginsSettings['installer'] = 'trickster/installer/';
            $pluginsSettings['project'] = 'project/';
            $data = [
                    'enabledPlugins' => $pluginsSettings,
                    'licenceKey' => $input['licence_key']
                ] + $mainConfig->getData();
            $projectConfig->setData($data);
            $projectConfig->save();
            $dbSettings = [
                'mysqlUser'               => $input['db_user'],
                'mysqlPassword'           => $input['db_password'],
                'mysqlDatabase'           => $input['db_database'],
                'mysqlHost'               => $input['db_host'],
                'mysqlTablesPrefix'       => $input['db_tables_prefix'],
                'mysqlConnectionEncoding' => 'utf8'
            ];
            $config = $configManager->getConfigFromPath(ROOT_PATH . 'project/config/transport.php');
            $config->setData($dbSettings);
            $configManager->saveConfig($config);
            $config = $configManager->getConfigFromPath(ROOT_PATH . 'project/config/statstransport.php');
            $config->setData($dbSettings);
            $configManager->saveConfig($config);
            $config = $configManager->getConfigFromPath(ROOT_PATH . 'project/config/dev/transport.php');
            $config->setData($dbSettings);
            $configManager->saveConfig($config);

            $updatesApi = $this->getService('UpdatesApi');
            // UpdatesApi may be instanced with old licence, hence this line:
            $updatesApi->setLicenceKey($input['licence_key']);
            try {
                $deployments = $updatesApi->getDeployments();
                $deploymentsIndex = [];
                // filter existing
                foreach ($deployments as $key => $deployment) {
                    if (in_array($deployment->type, $plugins) === false
                        || $deploymentsManager->isVersionInstalled($deployment->type, $deployment->version)
                    ) {
                        unset($deployments[$key]);
                        continue;
                    }
                    $deploymentsIndex[$deployment->type . '_' . $deployment->version] = true;
                }
                $deploymentsManager->clearPendingDeployments();
                foreach ($deployments as $deployment) {
                    foreach ($deployment->requirements as $versionInfo) {
                        $key = $versionInfo['type'] . '_' . $versionInfo['version'];
                        if (!isset($deploymentsIndex[$key])
                            && !$deploymentsManager->isVersionInstalled($versionInfo['type'], $versionInfo['version'])
                        ) {
                            $error = $deployment->type . '_' . $deployment->version
                                . ' requires installing ' . $key . '!';
                            break;
                        }
                    }
                    if ($error !== '') {
                        break;
                    }
                    $path = $updatesApi->downloadDeployment($deployment->id);
                    $deploymentsManager->addPendingDeployment($deployment->type, $deployment->version, $path);
                }
                if ($error === '') {
                    $installProgress = self::PROGRESS_DOWNLOADED;
                    $projectConfig->set('installProgress', $installProgress);
                    $projectConfig->save();
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        if ($error === '') {
            try {
                if ($deploymentsManager->hasPendingDeployments()) {
                    $deploymentsManager->installPendingDeployments();
                }
                $projectConfig->set('installProgress', self::PROGRESS_INSTALLED);
                $projectConfig->save();
                $deprecatedConfigPath = ROOT_PATH . 'config/';
                if (is_dir($deprecatedConfigPath) === true) {
                    $files = array_diff(scandir($deprecatedConfigPath), array('.', '..'));
                    foreach ($files as $file) {
                        unlink($deprecatedConfigPath . $file);
                    }
                    rmdir($deprecatedConfigPath);
                }
                $redirectUrl = $this->controller->baseURL . 'installer/';
                $this->controller->redirect($redirectUrl);
            } catch (Throwable $e) {
                // Executed only in PHP 7, will not match in PHP 5
                $error = 'Error during installation! ' . $e->getMessage();
            } catch (Exception $e) {
                // Executed only in PHP 5, will not be reached in PHP 7
                $error = 'Error during installation! ' . $e->getMessage();
            }
        }
        $this->renderer->assign('error', $error);
        $this->renderer->assign('input', $input);
        $this->actionIndex();
    }

    protected function handleDb($input, &$error)
    {
        $connection = @mysqli_connect(
            $input['db_host'],
            $input['db_user'],
            $input['db_password']
        );
        if (!$connection) {
            $error = 'Could not connect to DB server';
        } else {
            if (!mysqli_select_db($connection, $input['db_database'])
                && !mysqli_query($connection, "CREATE DATABASE {$input['db_database']}")
            ) {
                $error = "Error creating DB: " . mysqli_error($connection);
            }
            $connection->close();
        }
    }

    protected function installComposerPackages(&$error)
    {
        // 2>&1 to the end of your shell command to have STDERR returned as well as STDOUT.
        $output = shell_exec('composer install 2>&1');
        if (strpos($output, 'Generating autoload files') === false
            && strpos($output, 'Nothing to install or update') === false
        ) {
            $error = 'Something looks wrong in "composer install" command output';
        }
    }

    protected function setupTheme()
    {
        $designThemesManager = $this->getService('designThemesManager', ['currentThemeCode' => $this->applicationName]);
        $this->theme = $designThemesManager->getCurrentTheme();
    }
}

?>