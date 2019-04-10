<?php

class directoSyncApplication extends controllerApplication
{
    protected $applicationName = 'directoSync';
    public $rendererName = 'smarty';

    public function initialize()
    {
        set_time_limit(60 * 60);
        $this->startSession('crontab');
        $this->createRenderer();
    }

    public function execute($controller)
    {
        /**
         * @var Cache $cache
         */
        $cache = $this->getService('Cache');
        $cache->enable(false, false, true);

        $renderer = $this->getService('renderer');
        $renderer->endOutputBuffering();

        $user = $this->getService('user');
        if ($userId = $user->checkUser('crontab', null, true)) {
            $structureManager = $this->getService('structureManager', [
                'rootUrl' => $controller->rootURL,
                'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerAdmin'),
            ], true);

            $structureManager->setPrivilegeChecking(false);
            $languagesManager = $this->getService('languagesManager');

            $renderer = renderer::getInstance();
            $renderer->endOutputBuffering();
            $sync = $this->getService('DirectoSync');
            $sync->run();
            echo "Done, {$sync->getProductsUpdated()} products updated";
        }
    }
}