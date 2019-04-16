<?php

class dbServiceContainer extends DependencyInjectionServiceContainer
{
    public function makeInstance()
    {
        return $this->loadDb('transport');
    }

    public function makeInjections($instance)
    {
    }

    protected function loadDb($config)
    {
        /**
         * @var ConfigManager $configManager
         */
        $configManager = $this->registry->getService('ConfigManager');
        $dbConfig = $configManager->getConfig($config);
        if ($dbConfig->isEmpty()) {
            return null;
        }
        $capsule = new Illuminate\Database\Capsule\Manager();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $dbConfig->get('mysqlHost'),
            'database' => $dbConfig->get('mysqlDatabase'),
            'username' => $dbConfig->get('mysqlUser'),
            'password' => $dbConfig->get('mysqlPassword'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => $dbConfig->get('mysqlTablesPrefix'),
            'options' => [
                PDO::ATTR_PERSISTENT => true,
            ],
        ]);
        $capsule->setFetchMode(PDO::FETCH_ASSOC);
        if ($config == 'transport') {
            $capsule->setAsGlobal();
        }
        if ($pdo = $capsule->getConnection()->getPdo()) {
            $pdo->query('SET sql_mode = ""');
        }
        return $capsule->getConnection();
    }
}