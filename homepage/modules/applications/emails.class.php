<?php

use App\Logging\EventsLog;
use App\Users\CurrentUserService;

class emailsApplication extends controllerApplication
{
    public $rendererName = 'smarty';
    protected $applicationName = 'emails';

    public function initialize()
    {
        $this->startSession('emails', $this->getService('ConfigManager')->get('main.publicSessionLifeTime'));
        $this->createRenderer();
    }

    public function execute($controller)
    {
        $currentUserService = $this->getService(CurrentUserService::class);
        $user = $currentUserService->getCurrentUser();
        if ($userId = $user->checkUser('crontab', null, true)) {
            $user->switchUser($userId);

            $structureManager = $this->getService('structureManager', [
                'rootUrl' => $controller->rootURL,
                'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerAdmin'),
            ]);

            $this->processRequestParameters();

            if ($controller->getParameter('theme')) {
                $requestedTheme = $controller->getParameter('theme');
            } else {
                $requestedTheme = 'project';
            }

            $designThemesManager = $this->getService('DesignThemesManager');
            $currentTheme = $designThemesManager->getTheme($requestedTheme);
            /**
             * @var settingsManager $settingsManager
             */
            $settingsManager = $this->getService('settingsManager');
            $settings = $settingsManager->getSettingsList($this->getService('LanguagesManager')
                ->getCurrentLanguageId());
            $this->renderer->assign('settings', $settings);
            $this->renderer->assign('theme', $currentTheme);
            $this->renderer->assign('controller', $controller);
            $this->renderer->assign('unsubscribeLink', $this->unsubscribeLink);
            $this->renderer->setCacheControl('no-cache');
            $this->renderer->template = $currentTheme->template('unsubscribed.tpl');
            $this->renderer->display();
        }
    }

    public function processRequestParameters()
    {
        $controller = controller::getInstance();
        $action = false;
        if ($controller->getParameter('action')) {
            $action = $controller->getParameter('action');
        }

        if ($action == 'unsubscribe') {
            $email = $controller->getParameter('email');
            $key = $controller->getParameter('key');

            $this->logNewsMailEvents('newsMail_unsubscribe_1step');

            if ($email && $key) {
                if ($this->checkEmailKey($email, $key)) {
                    $collection = persistableCollection::getInstance('email_dispatchments_history');
                    if ($records = $collection->load(['id' => $controller->getParameter('id')])) {
                        $record = reset($records);
                        $emailDispatcher = $this->getService('EmailDispatcher');
                        if ($emailDispatcher->getDispatchment($record->dispatchmentId)) {
                            $receiver = new EmailDispatchmentReceiver($record);
                            $count = 1;
                            $this->unsubscribeLink = str_replace('unsubscribe', 'confirm', $receiver->getUnsubscribeLink(), $count);
                        }
                    }

                    return true;
                }
            }
            exit;
        } elseif ($action == 'confirm') {
            $email = $controller->getParameter('email');
            $key = $controller->getParameter('key');

            $this->logNewsMailEvents('newsMail_unsubscribe');

            if ($email && $key) {
                if ($this->checkEmailKey($email, $key) || true) {
                    $this->unsubscribeEmail($email);
                }
            }
        } elseif ($action == 'view' || $action == 'viewOnline') {
            if ($action == 'view') {
                $this->logNewsMailEvents('newsMail_emailOpened');
            }
            if ($action == 'viewOnline') {
                $this->logNewsMailEvents('newsMail_viewFromBrowser');
            }
            $dispatchmentId = $controller->getParameter('id');
            $email = $controller->getParameter('email');
            $key = $controller->getParameter('key');
            if ($email && $key) {
                if ($this->checkEmailKey($email, $key)) {
                    $this->viewEmail($dispatchmentId);
                }
            }
        } elseif ($action == 'viewURL') {
            $destination = $controller->getParameter('url');
            $destination = base64_decode($destination);
            $this->logNewsMailEvents('newsMail_linkClicked', $destination);
            $controller->redirect($destination);
        } elseif ($action == 'viewImage') {
            ErrorLog::getInstance()
                ->logMessage('public templates', 'Deprecated method used, please use $dispatchmentType->getTrackedBlankImage() to load an empty image instead of getTrackedImageUrl on logo image');
            $this->logNewsMailEvents('newsMail_emailOpened');
            $imagefullurl = $_SERVER['REQUEST_URI'];
            $repls = [
                '/email:' => '/email/',
            ];
            $imagefullurl = strtr($imagefullurl, $repls);
            $imagefullurlParts = explode('/', $imagefullurl);
            foreach ($imagefullurlParts as $key => $value) {
                if ($value == 'emails' || substr($value, 0, 2) == 'id' || substr($value, 0, 6) == 'action') {
                    unset($imagefullurlParts[$key]);
                }
            }
            $imageUrl = ROOT_PATH . implode('/', $imagefullurlParts);
            echo readfile($imageUrl);
            exit;
        } elseif ($action == 'viewBlankImage') {
            $this->logNewsMailEvents('newsMail_emailOpened');
            header('Content-Type: image/gif');
            echo base64_decode("R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");
            exit;
        } elseif ($action == 'viewExternalURL') {
            $this->logNewsMailEvents('newsMail_externalLinkClicked');
        }
    }

    protected function logNewsMailEvents($type, $url = null)
    {
        $eventLogger = $this->getService(EventsLog::class);
        $controller = controller::getInstance();
        $dispatchmentId = $controller->getParameter('id');
        $newsMailsAddress = '';
        $collection = persistableCollection::getInstance('email_dispatchments_history');
        if ($records = $collection->load(['id' => $dispatchmentId])) {
            $record = reset($records);
            $dispatchmentIdForLogs = $record->dispatchmentId;
            $newsMailsAddress = $record->email;
        }
        $newsmailId = false;
        $collection = persistableCollection::getInstance('email_dispatchments');
        if ($records = $collection->load(['id' => $dispatchmentIdForLogs])) {
            $record = reset($records);
            $newsmailId = $record->referenceId;
        }
        $visitorManager = $this->getService(VisitorsManager::class);
        $event = new Event();
        $event->setElementId($newsmailId);
        $event->setType($type);
        $event->setVisitorId($visitorManager->getVisitorIdFromEmail($newsMailsAddress));
        $parameters = [];
        if ($type == 'newsMail_linkClicked') {
            $external = $controller->getParameter('external');
            $targetId = $newsmailId;
            if (!$external) {
                $structureManager = $this->getService('structureManager', [
                    'rootUrl' => $controller->rootURL,
                    'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
                ], true);
                $urlComponents = parse_url($url) ?: [];
                $path = isset($urlComponents['path']) ? $urlComponents['path'] : '';
                $pathSegments = array_filter(explode('/', $path));
                $structureManager->setRequestedPath($pathSegments);
                if ($currentElement = $structureManager->getCurrentElement($pathSegments)) {
                    $targetId = $currentElement->id;
                } else {
                    // file requested or element deleted
                }
            } else {
                $event->setType('newsMail_externalLinkClicked');
            }
            $type = $external ? 'newsMail_externalLinkClicked' : $type;
            $parameters['destinationElementId'] = $targetId;
            $parameters['destinationUrl'] = $url;
        }
        $event->setParameters($parameters);
        if ($newsmailId) {
            $this->eventId = $eventLogger->saveEvent($event);
        }
        if ($type === 'newsMail_linkClicked' || $type === 'newsMail_externalLinkClicked') {
            $this->makeLinkUriEvent($url);
        }
    }

    protected function makeLinkUriEvent($uri)
    {
        $db = $this->getService('db');
        if (!empty($this->eventId)) {
            $uriId = $db->table('visitor_uri')
                ->where('visitor_uri.uri', '=', $uri)
                ->select('visitor_uri.id')
                ->first();
            if (empty($uriId)) {
                $uriId['id'] = $db->table('visitor_uri')
                    ->insertGetId(
                        ['uri' => $uri]
                    );
            }
            $db->table('link_event_uri')
                ->insertGetId(
                    ['eventId' => $this->eventId, 'uriId' => $uriId['id']]
                );
        }
    }

    protected function checkEmailKey($email, $key)
    {
        // constant deprecated since 2016.03
        $secret = defined('EMAIL_DISPATCHMENT_SECRET')
            ? EMAIL_DISPATCHMENT_SECRET
            : $this->getService('ConfigManager')->get('emails.dispatchmentSecret');
        if ($key == hash_hmac('sha256', $email, $secret)) {
            return true;
        }
        return false;
    }

    protected function viewEmail($dispatchmentId)
    {
        $html = '';
        $collection = persistableCollection::getInstance('email_dispatchments_history');
        if ($records = $collection->load(['id' => $dispatchmentId])) {
            $record = reset($records);
            $emailDispatcher = $this->getService('EmailDispatcher');
            if ($originalDispatchment = $emailDispatcher->getDispatchment($record->dispatchmentId)) {
                $receiver = new EmailDispatchmentReceiver($record);
                $originalDispatchment->setReceiver($receiver);
                $html = $originalDispatchment->getContent();
            }
        }

        //todo: use renderer in this case instead
        echo $html;
        exit;
    }

    protected function unsubscribeEmail($email)
    {
        $structureManager = $this->getService('structureManager');
        $collection = persistableCollection::getInstance('module_newsmailaddress');
        $columns = ['id'];

        $conditions = [];
        $conditions[] = [
            'column' => 'email',
            'action' => '=',
            'argument' => $email,
        ];

        if ($result = $collection->conditionalLoad($columns, $conditions, [], 1)) {
            $idList = [];
            foreach ($result as &$row) {
                $idList[] = $row['id'];
            }
            if (count($idList)) {
                $newsMailsAddressesElementId = $structureManager->getElementIdByMarker("newsMailsAddresses");
                if ($mailsElement = $structureManager->getElementById($newsMailsAddressesElementId, null, true)) {
                    if ($elements = $structureManager->getElementsByIdList($idList, $mailsElement->id, true)) {
                        foreach ($elements as $element) {
                            $element->deleteElementData();
                        }
                    }
                }
            }
        }
    }
}




