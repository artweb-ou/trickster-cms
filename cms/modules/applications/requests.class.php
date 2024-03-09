<?php

use App\Logging\FormattedLogRecordDTO;
use App\Logging\RedisRequestLogger;

class requestsApplication extends controllerApplication
{
    protected $applicationName = '';
    public $rendererName = 'smarty';
    public $themeCode = '';
    /**
     * @var Config
     */
    public $config;

    public function initialize()
    {
        $this->createRenderer();
    }

    public function execute($controller)
    {
        /** @var RedisRequestLogger $RedisRequestLogger */
        $RedisRequestLogger = $this->getService('RedisRequestLogger');

        try {
            $requests = $RedisRequestLogger->getAllLogs();

            $topLongestRequests = $this->getTopLongestRequests($requests);

            $ipCount = [];
            $ipDuration = [];

            foreach ($requests as $request) {
                $ip = $request->ip;
                if (!isset($ipCount[$ip])) {
                    $ipCount[$ip] = 0;
                }
                $ipCount[$ip]++;

                if (!isset($ipDuration[$ip])) {
                    $ipDuration[$ip] = 0.0;
                }
                $ipDuration[$ip] += round($request->duration);
            }

            arsort($ipCount);
            $topIpCount = array_slice($ipCount, 0, 10);

            arsort($ipDuration);
            $topIpDuration = array_slice($ipDuration, 0, 10);

            $this->renderer->assign('topIpCount', $topIpCount);
            $this->renderer->assign('topIpDuration', $topIpDuration);
            $this->renderer->assign('topLongestRequests', $this->makeFormatted($topLongestRequests));

        } catch (Exception) {

        }
        $this->renderer->assign('requests', $this->makeFormatted($requests));
        $this->renderer->setContentDisposition('inline');
        $this->renderer->setContentType('text/html');

        $this->renderer->setCacheControl('no-cache');

        $pathsManager = $this->getService('PathsManager');
        $this->renderer->template = $pathsManager->getIncludeFilePath('templates/requests/list.tpl');;

        $this->renderer->display();
    }

    private function getTopLongestRequests(array $requests): array
    {
        usort($requests, function ($request1, $request2) {
            return $request2->duration - $request1->duration;
        });

        return array_slice($requests, 0, 20);
    }
    private function makeFormatted($requests){
        $formatted = [];
        foreach ($requests as $request) {
            $dateTime = (new DateTime())->setTimestamp((int)($request->startTime));
            $formattedStartTime = $dateTime->format('d.m.Y H:i:s');

            $formattedDuration = number_format($request->duration, 2, '.', '');

            $formatted[] = new FormattedLogRecordDTO(
                $request->requestId,
                $request->ip,
                $request->url,
                $request->userAgent,
                $formattedStartTime,
                $formattedDuration
            );
        }
        return $formatted;
    }
    public function getUrlName()
    {
        return '';
    }

    public function getThemeCode()
    {
        return $this->themeCode;
    }
}

