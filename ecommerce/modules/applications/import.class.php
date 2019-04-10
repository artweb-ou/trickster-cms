<?php

class importApplication extends controllerApplication
{
    protected $applicationName = 'import';
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
            $user->switchUser($userId);
            $structureManager = $this->getService('structureManager', [
                'rootUrl' => $controller->rootURL,
                'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerAdmin'),
            ], true);

            $pluginsElementId = $structureManager->getElementIdByMarker('importPlugins');
            $importPlugins = [];
            if ($pluginsElementId) {
                $pluginElementsIds = $this->getService('linksManager')
                    ->getConnectedIdList($pluginsElementId, 'structure', 'parent');
                if ($pluginElementsIds) {
                    $importPlugins = $structureManager->getElementsByIdList($pluginElementsIds);
                }
            }
            $quickImport = (bool)$controller->getParameter('quick');
            $targetIndex = [];
            if ($parameter = $controller->getParameter('plugins')) {
                $targetIndex = array_flip(explode(',', $parameter));
            }
            foreach ($importPlugins as &$importPlugin) {
                if (!$targetIndex || isset($targetIndex[$importPlugin->id])) {
                    $importPlugin->import($quickImport);
                }
            }
        }
    }
}

