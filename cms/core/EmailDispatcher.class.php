<?php

class EmailDispatcher extends errorLogger
{
    protected $timeLimit;
    protected $oneDispatchmentDelay;
    protected $designThemesManager;

    public function __construct()
    {
        $this->timeLimit = 10;
        $this->oneDispatchmentDelay = 1;
    }

    /**
     * @param mixed $designThemesManager
     */
    public function setDesignThemesManager($designThemesManager)
    {
        $this->designThemesManager = $designThemesManager;
    }

    /**
     * @return designThemesManager
     */
    public function getDesignThemesManager()
    {
        if (!$this->designThemesManager) {
            $controller = controller::getInstance();
            //only for possible backwards compatibility, use DI instead!
            $configManager = $controller->getConfigManager();
            $pathsManager = $controller->getPathsManager();
            $this->designThemesManager = new designThemesManager();
            $themesPath = $pathsManager->getRelativePath('themes');
            foreach ($pathsManager->getIncludePaths() as $path) {
                $this->designThemesManager->setThemesDirectoryPath($path . $themesPath);
            }
            $this->designThemesManager->setCurrentThemeCode($configManager->get('main.publicTheme'));
        }
        return $this->designThemesManager;
    }

    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    public function setOneDispatchmentDelay($oneDispatchmentDelay)
    {
        $this->oneDispatchmentDelay = $oneDispatchmentDelay;
    }

    /**
     *
     */
    public function dispatchAwaitingList()
    {
        $startTime = time();
        if ($historyList = $this->getDispatchmentsToSend()) {
            foreach ($historyList as &$item) {
                if ($dispatchment = $this->getDispatchment($item['dispatchmentId'])) {
                    $currentTime = time();
                    while (($currentTime <= $startTime + $this->timeLimit) && $dispatchment->dispatchAwaitingItem()) {
                        if ($this->oneDispatchmentDelay) {
                            sleep($this->oneDispatchmentDelay);
                        }
                        $currentTime = time();
                    }
                }
            }
        }
    }

    protected function getDispatchmentsToSend()
    {
        $history = [];
        $collection = persistableCollection::getInstance('email_dispatchments_history');

        $conditions = [
            [
                'column' => 'status',
                'action' => '=',
                'argument' => 'awaiting',
            ],
            [
                'column' => 'startTime',
                'action' => '<=',
                'argument' => time(),
            ],
        ];
        $order = [
            'priority' => 'asc',
            'startTime' => 'asc',
        ];
        if ($rows = $collection->conditionalLoad(null, $conditions, $order)) {
            foreach ($rows as &$row) {
                if ($row['referenceId'] > 0) {
                    $history[$row['referenceId']] = $row;
                }
            }
        }

        return $history;
    }

    /**
     * @param EmailDispatchment $emailDispatchment
     * @return bool
     */
    public function startDispatchment($emailDispatchment)
    {
        $result = false;
        $emailDispatchment->persist();
        $startTime = time();
        $currentTime = $startTime;
        while ($currentTime <= $startTime + $this->timeLimit && $status = $emailDispatchment->dispatchAwaitingItem()) {
            if ($status == 'success') {
                $result = true;
            }
            $currentTime = time();
        }
        return $result;
    }

    /**
     * @param EmailDispatchment $emailDispatchment
     */
    public function appointDispatchment($emailDispatchment)
    {
        $emailDispatchment->persist();
    }

    public function getReferencedDispatchmentHistory($referenceId)
    {
        $history = false;
        if ($dispatchments = $this->loadReferencedDispatchments($referenceId)) {
            $history = $this->getAggregatedDispatchmentsHistory($dispatchments);
        }
        return $history;
    }

    /**
     * @param int $referenceId
     * @return EmailDispatchment[]
     */
    protected function loadReferencedDispatchments($referenceId)
    {
        $dispatchments = [];

        $collection = persistableCollection::getInstance('email_dispatchments');
        $conditions = [
            [
                'column' => 'referenceId',
                'action' => '=',
                'argument' => $referenceId,
            ],
        ];
        if ($rows = $collection->conditionalLoad('id', $conditions, ['startTime' => 'asc'])) {
            foreach ($rows as &$row) {
                if ($dispatchment = $this->getDispatchment($row['id'])) {
                    $dispatchments[] = $dispatchment;
                }
            }
        }

        return $dispatchments;
    }

    /**
     * @param EmailDispatchment[] $dispatchments
     * @return array
     */
    protected function getAggregatedDispatchmentsHistory($dispatchments)
    {
        $history = [];
        $collection = persistableCollection::getInstance('email_dispatchments_history');

        foreach ($dispatchments as &$dispatchment) {
            $dispatchmentId = $dispatchment->getId();
            $conditions = [
                [
                    'column' => 'dispatchmentId',
                    'action' => '=',
                    'argument' => $dispatchmentId,
                ],
            ];
            if ($rows = $collection->conditionalLoad(null, $conditions, ['startTime' => 'asc'])) {
                foreach ($rows as &$row) {
                    if ($row['referenceId'] > 0) {
                        $history[$row['referenceId']] = $row;
                    }
                }
            }
        }
        return $history;
    }

    public function cancelReferencedDispatchments($referenceId)
    {
        if ($dispatchments = $this->loadReferencedDispatchments($referenceId)) {
            foreach ($dispatchments as &$dispatchment) {
                $dispatchment->cancelSending();
            }
        }
    }

    public function getDispatchment($dispatchmentId)
    {
        $result = false;
        $collection = persistableCollection::getInstance('email_dispatchments');
        if ($objects = $collection->load(['id' => $dispatchmentId])) {
            $data = reset($objects);
            $dispatchment = $this->getEmptyDispatchment();
            $dispatchment->setPersistedData($data);
            $result = $dispatchment;
        }
        return $result;
    }

    public function getEmptyDispatchment()
    {
        $emailDispatchment = new EmailDispatchment();
        $emailDispatchment->setEmailDispatcher($this);
        return $emailDispatchment;
    }

    public function clearOutdatedDispatchmentsData()
    {
        $collection = persistableCollection::getInstance('email_dispatchments');

        $conditions = [
            [
                'column' => 'dataLifeTime + startTime',
                'action' => '<',
                'argument' => time(),
                'literal' => true,
            ],
            [
                'column' => 'data',
                'action' => '!=',
                'argument' => '',
            ],
        ];

        if ($rows = $collection->conditionalLoad(['id'], $conditions)) {
            foreach ($rows as &$row) {
                if ($dispatchment = $this->getDispatchment($row['id'])) {
                    $dispatchment->clearData();
                }
            }
        }
    }
}

class EmailDispatchment
{
    protected $id;
    protected $referenceId;
    protected $type;
    protected $receiversList;
    protected $attachmentsList;
    protected $data;
    protected $startTime;
    protected $dataLifeTime;
    protected $priority;
    protected $fromName;
    protected $fromEmail;
    protected $subject;
    protected $unsubscribeLink;
    /**
     * @var EmailDispatchmentReceiver
     */
    protected $receiver;
    protected $emailDispatcher;

    /**
     * @param EmailDispatcher $emailDispatcher
     */
    public function setEmailDispatcher($emailDispatcher)
    {
        $this->emailDispatcher = $emailDispatcher;
    }

    /**
     * @return EmailDispatcher
     */
    public function getEmailDispatcher()
    {
        return $this->emailDispatcher;
    }

    /**
     * @param mixed $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    public function __construct()
    {
        $this->dataLifeTime = 60 * 60 * 24 * 365 * 7;
        $this->priority = 0;
        $this->startTime = time();
        $this->receiversList = [];
        $this->attachmentsList = [];
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setData($data)
    {
        $this->data = json_encode($data);
    }

    public function mb_unserialize($string)
    {
        $string = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($m) {
                $len = strlen($m[2]);
                $result = "s:$len:\"{$m[2]}\";";
                return $result;
            },
            $string
        );
        return unserialize($string);
    }

    public function getData()
    {
        $data = json_decode($this->data, true);
        if ($data === null) {
            $data = $this->mb_unserialize($this->data);
        }
        return $data;
    }

    public function setDataLifeTime($dataLifeTime)
    {
        $this->dataLifeTime = $dataLifeTime;
    }

    public function getDataLifeTime()
    {
        return $this->dataLifeTime;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function registerReceiver($email, $name = null, $referenceId = null)
    {
        $this->receiversList[] = [
            'email' => $email,
            'name' => $name,
            'referenceId' => $referenceId,
        ];
    }

    public function registerAttachment($filePath, $fileName)
    {
        $this->attachmentsList[] = [
            'filePath' => $filePath,
            'fileName' => $fileName,
        ];
    }

    public function getReceiversList()
    {
        return $this->receiversList;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }

    public function getReferenceId()
    {
        return $this->referenceId;
    }

    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    public function getFromName()
    {
        return $this->fromName;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function clearData()
    {
        $collection = persistableCollection::getInstance('email_dispatchments');
        foreach ($collection->load(['id' => $this->id]) as $dataObject) {
            $dataObject->data = '';
            $dataObject->persist();
        }
    }

    public function persist()
    {
        $collection = persistableCollection::getInstance('email_dispatchments');
        $dataObject = $collection->getEmptyObject();
        $dataObject->referenceId = $this->referenceId;
        $dataObject->type = $this->type;
        $dataObject->data = $this->data;
        $dataObject->startTime = $this->startTime;
        $dataObject->dataLifeTime = $this->dataLifeTime;
        $dataObject->priority = $this->priority;
        $dataObject->fromName = $this->fromName;
        $dataObject->fromEmail = $this->fromEmail;
        $dataObject->subject = $this->subject;

        $dataObject->persist();

        $this->id = $dataObject->id;

        $collection = persistableCollection::getInstance('email_dispatchments_history');
        foreach ($this->receiversList as &$receiverData) {
            $dataObject = $collection->getEmptyObject();
            $dataObject->referenceId = $receiverData['referenceId'];
            $dataObject->dispatchmentId = $this->id;
            $dataObject->name = $receiverData['name'];
            $dataObject->email = $receiverData['email'];
            $dataObject->startTime = $this->startTime;
            $dataObject->status = 'awaiting';

            $dataObject->persist();
        }
        $collection = persistableCollection::getInstance('email_dispatchments_attachments');
        foreach ($this->attachmentsList as &$attachmentData) {
            $dataObject = $collection->getEmptyObject();
            $dataObject->dispatchmentId = $this->id;
            $dataObject->fileName = $attachmentData['fileName'];
            $dataObject->filePath = $attachmentData['filePath'];

            $dataObject->persist();
        }
    }

    public function setPersistedData($dataObject)
    {
        $this->id = $dataObject->id;
        $this->referenceId = $dataObject->referenceId;
        $this->type = $dataObject->type;
        $this->data = $dataObject->data;
        $this->startTime = $dataObject->startTime;
        $this->dataLifeTime = $dataObject->dataLifeTime;
        $this->priority = $dataObject->priority;
        $this->fromName = $dataObject->fromName;
        $this->fromEmail = $dataObject->fromEmail;
        $this->subject = $dataObject->subject;
    }

    public function cancelSending()
    {
        $collection = persistableCollection::getInstance('email_dispatchments_history');
        if ($objects = $collection->load([
            'dispatchmentId' => $this->id,
            'status' => 'awaiting',
        ])
        ) {
            foreach ($objects as &$object) {
                if ($object->status == 'awaiting') {
                    $object->status = 'cancelled';
                    $object->persist();
                }
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Dispatches one email receiver from email dispatchers receivers.
     * Returns false if there are no receivers left, otherwise returns string status of email sending
     *
     * @return bool|string
     */
    public function dispatchAwaitingItem()
    {
        $result = false;

        if ($this->receiver = $this->getNextAwaitingItem()) {
            if ($content = $this->getContent()) {
                $this->receiver->setStatus('inprogress');
                $this->receiver->persist();

                $result = 'fail';
                if ($this->sendEmail($content, $this->receiver->getEmail())) {
                    $result = 'success';
                }

                $this->receiver->setStatus($result);
                $this->receiver->persist();
            }
        }

        return $result;
    }

    public function getContent($preview = false)
    {
        $renderer = new EmailDispatchmentRenderer();
        $renderer->setDesignThemesManager($this->getEmailDispatcher()->getDesignThemesManager());
        $renderer->setData($this->getData());
        $renderer->setType($this->getType());
        $renderer->setDispatchment($this);

        if (!$preview && $this->receiver) {
            $renderer->setUnsubscribleLink($this->receiver->getUnsubscribeLink());
            $renderer->setWebLink($this->receiver->getWebLink());
            $renderer->setReceiverEmail($this->receiver->getEmail());
            $renderer->setReceiverName($this->receiver->getName());
        }

        $renderer->setFromEmail($this->getFromEmail());
        $renderer->setFromName($this->getFromName());
        $renderer->setSubject($this->getSubject());
        $renderer->setDispatchmentId($this->getId());
        return $renderer->renderContent();
    }

    protected function sendEmail($content, $receiverEmail)
    {
        $sender = new EmailDispatchmentSender();
        return $sender->sendEmail($content, $receiverEmail, $this->fromEmail, $this->fromName, $this->subject, $this->loadAttachmentsList(), $this->receiver->getUnsubscribeLink());
    }

    protected function getNextAwaitingItem()
    {
        $receiver = false;
        $collection = persistableCollection::getInstance('email_dispatchments_history');
        $order = ['startTime' => 'asc'];
        if ($objects = $collection->load([
            'dispatchmentId' => $this->id,
            'status' => 'awaiting',
        ], $order, false, 1)
        ) {
            $dispatchment = reset($objects);
            $receiver = new EmailDispatchmentReceiver($dispatchment);
        }
        return $receiver;
    }

    public function getType()
    {
        return $this->type;
    }

    protected function loadAttachmentsList()
    {
        if (is_null($this->attachmentsList)) {
            $this->attachmentsList = [];
            $collection = persistableCollection::getInstance('email_dispatchments_history');
            if ($objects = $collection->load(['dispatchmentId' => $this->id])) {
                foreach ($objects as &$object) {
                    $this->attachmentsList[] = [
                        "filePath" => $object->filePath,
                        "fileName" => $object->fileName,
                    ];
                }
            }
        }
        return $this->attachmentsList;
    }
}

class EmailDispatchmentReceiver extends errorLogger
{
    protected $historyId;
    protected $email;
    protected $name;
    protected $unsubscribeLink;
    protected $webLink;
    /**
     * @var persistableObject
     */
    protected $persistableObject;

    public function __construct(&$persistableObject)
    {
        $this->persistableObject = $persistableObject;
        $this->historyId = $persistableObject->id;
        $this->email = $persistableObject->email;
        $this->name = $persistableObject->name;
        $this->unsubscribeLink = $this->getUnsubscribeLink();
    }

    public function getUnsubscribeLink()
    {
        if (is_null($this->unsubscribeLink)) {
            $controller = controller::getInstance();
            $secret = defined('EMAIL_DISPATCHMENT_SECRET')
                ? EMAIL_DISPATCHMENT_SECRET
                : $controller->getConfigManager()
                    ->get('emails.dispatchmentSecret'); // constant deprecated since 2016.03
            $key = hash_hmac('sha256', $this->email, $secret);
            $this->unsubscribeLink = $controller->baseURL . 'emails/action:unsubscribe/email:' . urlencode($this->email) . '/key:' . $key . '/id:' . $this->historyId;
        }
        return $this->unsubscribeLink;
    }

    public function getWebLink()
    {
        if (is_null($this->webLink)) {
            $controller = controller::getInstance();
            $secret = defined('EMAIL_DISPATCHMENT_SECRET')
                ? EMAIL_DISPATCHMENT_SECRET
                : $controller->getConfigManager()
                    ->get('emails.dispatchmentSecret'); // constant deprecated since 2016.03
            $key = hash_hmac('sha256', $this->email, $secret);
            $this->webLink = $controller->baseURL . 'emails/action:viewOnline/email:' . urlencode($this->email) . '/id:' . $this->historyId . '/key:' . $key;
        }
        return $this->webLink;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setStatus($newStatus)
    {
        $this->persistableObject->status = $newStatus;
    }

    public function persist()
    {
        $this->persistableObject->persist();
    }
}

class EmailDispatchmentRenderer extends errorLogger
{
    protected $receiverEmail;
    protected $receiverName;
    protected $fromEmail;
    protected $fromName;
    protected $subject;
    protected $data;
    protected $type;
    protected $dispatchment;
    protected $dispatchmentId;
    protected $unsubscribeLink;
    protected $webLink;
    protected $designThemesManager;

    /**
     * @return designThemesManager
     */
    public function getDesignThemesManager()
    {
        return $this->designThemesManager;
    }

    /**
     * @param designThemesManager $designThemesManager
     */
    public function setDesignThemesManager($designThemesManager)
    {
        $this->designThemesManager = $designThemesManager;
    }

    public function setUnsubscribleLink($unsubscribeLink)
    {
        $this->unsubscribeLink = $unsubscribeLink;
    }

    public function setWebLink($webLink)
    {
        $this->webLink = $webLink;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setDispatchment($dispatchment)
    {
        $this->dispatchment = $dispatchment;
    }

    public function getDispatchment()
    {
        return $this->dispatchment;
    }

    public function renderContent()
    {
        $content = false;
        if ($emailType = $this->getEmailDispatchmentType()) {
            $emailCss = $this->renderCss($emailType->getCssFiles(), $emailType->getCssImagesURL());
            $emailHTML = $this->renderHtml($emailType->getEmailTemplate(), $emailType->getContentTemplate(), $emailType->getDisplayWebLink(), $emailType->getDisplayUnsubscribeLink(), $emailType->getCssImagesURL(), $emailType, $emailType->isLinksTrackingEnabled());
            $content = $this->applyCssToHtml($emailCss, $emailHTML);
        }

        return $content;
    }

    protected function applyCssToHtml($emailCss, $emailHTML)
    {
        $content = false;
        try {
            $emogrifier = new \Pelago\Emogrifier();
            $emogrifier->setCSS($emailCss);
            $emogrifier->setHTML($emailHTML);
            $emogrifier->disableInvisibleNodeRemoval();
            //            $emogrifier->enableCssToHtmlMapping();
            $content = $emogrifier->emogrify();
        } catch (exception $ex) {
            $this->logError('emogrifier error: ' . $ex->getMessage());
        }
        return $content;
    }

    protected function renderHtml(
        $emailTemplate,
        $contentTemplate,
        $displayWebLink,
        $displayUnsubscribeLink,
        $imagesUrl,
        EmailDispatchmentType $emailType,
        $trackLinks
    ) {
        $controller = controller::getInstance();
        $htmlRenderer = renderer::getPlugin('smarty');

        if ($displayWebLink && $this->webLink) {
            $htmlRenderer->assign('webLink', $this->webLink);
        }

        if ($imagesUrl) {
            $htmlRenderer->assign('imagesUrl', $imagesUrl);
        }

        if ($displayUnsubscribeLink && $this->unsubscribeLink) {
            $htmlRenderer->assign('unsubscribeLink', $this->unsubscribeLink);
        }

        $htmlRenderer->assign('controller', $controller);
        $htmlRenderer->assign('data', $this->data);
        $htmlRenderer->assign('contentTheme', $emailType->getContentTheme());
        $htmlRenderer->assign('theme', $emailType->getTheme());
        $htmlRenderer->template = $emailTemplate;
        $htmlRenderer->assign('contentTemplate', $contentTemplate);
        $htmlRenderer->assign('dispatchmentType', $emailType);
        $htmlRenderer->assign('dispatchment', $this->getDispatchment());
        $result = $htmlRenderer->fetch();

        $dpId = $this->getDispatchmentHistoryId();
        if ($trackLinks && $dpId && $controller->domainName) {
            // if dispatchment ID doesn't exist, email preview is being rendered
            $doc = new DOMDocument();
            $doc->loadHTML($result);
            $links = $doc->getElementsByTagName('a');

            foreach ($links as $link) {
                if ($linkAddr = $link->getAttribute('href')) {
                    if (
                        substr($linkAddr, 0, 1) != '#' &&
                        $linkAddr != $this->webLink &&
                        $linkAddr != $this->unsubscribeLink &&
                        (
                            substr($linkAddr, 0, 2) == '//' ||
                            substr($linkAddr, 0, 7) == 'http://' ||
                            substr($linkAddr, 0, 8) == 'https://'
                        )
                    ) {
                        $parsedUrl = parse_url($linkAddr);
                        $parsedHost = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
                        $trackingUrl = $controller->domainURL . '/emails/id:' . $dpId . '/action:viewURL/url:' . base64_encode($linkAddr) . '/';
                        $externalLink = strcasecmp($controller->domainName, $parsedHost) !== 0;
                        if ($externalLink) {
                            $trackingUrl .= 'external:1/';
                        }
                        $link->setAttribute('href', $trackingUrl);
                    }
                }
            }
            $result = $doc->saveHTML();
        }
        return $result;
    }

    public function getDispatchmentHistoryId()
    {
        $dispatchmentId = $this->dispatchmentId;
        $receiverEmail = $this->receiverEmail;
        $collection = persistableCollection::getInstance('email_dispatchments_history');
        if ($records = $collection->load(['dispatchmentId' => $dispatchmentId, 'email' => $receiverEmail])) {
            $record = reset($records);
            $dpId = $record->id;
        }
        return $dpId;
    }

    // http://php.net/manual/en/function.parse-url.php#106731
    public function unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    protected function renderCss($cssFiles, $imagesURL)
    {
        $emailCss = '';
        try {
            $css = '@image_folder: "' . $imagesURL . '";';
            foreach ($cssFiles as &$filePath) {
                $css .= file_get_contents($filePath);
            }
            $less = new lessc();
            $emailCss = $less->compile($css);
        } catch (exception $ex) {
            $this->logError('lessphp error: ' . $ex->getMessage());
        }
        return $emailCss;
    }

    /**
     * @return EmailDispatchmentType
     */
    protected function getEmailDispatchmentType()
    {
        $object = false;
        $className = $this->type . 'EmailDispatchmentType';
        if (!class_exists($className, false)) {
            $fileName = $this->type . '.class.php';
            $pathsManager = controller::getInstance()->getPathsManager();
            $fileDirectory = $pathsManager->getRelativePath('dispatchmentTypes');
            if ($filePath = $pathsManager->getIncludeFilePath($fileDirectory . $fileName)) {
                include_once($filePath);
            }
        }
        if (class_exists($className, false)) {
            /**
             * @var EmailDispatchmentType $object
             */
            $object = new $className();
            $object->setEmailDispatchmentRenderer($this);
            $object->initialize();
        } else {
            $this->logError('EmailDispatchmentType class "' . $className . '" is missing');
        }
        return $object;
    }

    public function setDispatchmentId($dispatchmentId)
    {
        $this->dispatchmentId = $dispatchmentId;
    }

    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setReceiverEmail($receiverEmail)
    {
        $this->receiverEmail = $receiverEmail;
    }

    public function setReceiverName($receiverName)
    {
        $this->receiverName = $receiverName;
    }
}

class EmailDispatchmentSender extends errorLogger
{
    public function sendEmail(
        $content,
        $receiverEmail,
        $fromEmail,
        $fromName,
        $subject,
        $attachmentsList = [],
        $unsubscribeLink = null
    ) {
        $phpmailerObject = new PHPMailer();

        $phpmailerObject->IsHTML(true);
        $phpmailerObject->CharSet = 'UTF-8';
        $phpmailerObject->WordWrap = 64;
        $phpmailerObject->Encoding = 'base64';

        $configManager = controller::getInstance()->getConfigManager();
        $emailsConfig = $configManager->getConfig('emails');
        $transport = $emailsConfig->get('transport');
        $smtpHost = $emailsConfig->get('smtpHost');
        $smtpPort = $emailsConfig->get('smtpPort');
        $smtpUser = $emailsConfig->get('smtpUser');
        $smtpPass = $emailsConfig->get('smtpPassword');

        switch (strtoupper($transport)) {
            case 'SMTP':
                $phpmailerObject->IsSMTP();
                $phpmailerObject->SMTPAuth = true;
                $phpmailerObject->Host = $smtpHost;
                $phpmailerObject->Port = $smtpPort;
                $phpmailerObject->Username = $smtpUser;
                $phpmailerObject->Password = $smtpPass;
                break;
            default:
            case 'MAIL':
                $phpmailerObject->IsMail();
                break;
        }
        $phpmailerObject->Body = $content;
        $phpmailerObject->AltBody = $this->htmlToPlainText($content);
        $phpmailerObject->AddAddress($receiverEmail);
        $phpmailerObject->From = $fromEmail;
        $phpmailerObject->FromName = $fromName;
        $phpmailerObject->Subject = $subject;

        if ($unsubscribeLink) {
            $phpmailerObject->AddCustomHeader("List-Unsubscribe: " . $unsubscribeLink);
        }

        foreach ($attachmentsList as &$attachmentInfo) {
            if (is_file($attachmentInfo['filePath'])) {
                $phpmailerObject->AddAttachment($attachmentInfo['filePath'], $attachmentInfo['fileName']);
            }
        }

        if ($phpmailerObject->Send()) {
            $result = true;
        } else {
            $this->logError($phpmailerObject->ErrorInfo);
            $result = false;
        }
        return $result;
    }

    protected function htmlToPlainText($src)
    {
        $result = $src;
        $result = html_entity_decode($result, ENT_QUOTES);
        $result = preg_replace('/<style([\s\S]*?)<\/style>/', '', $result); // remove stylesheet
        $result = preg_replace('/[\xA0]*/', '', $result);
        $result = preg_replace('#[\n\r\t]#', "", $result);
        $result = preg_replace('#[\s]+#', " ", $result);
        $result = preg_replace('#(</li>|</div>|</td>|</tr>|<br />|<br/>|<br>)#', "$1\n", $result);
        $result = preg_replace('#(</h1>|</h2>|</h3>|</h4>|</h5>|</p>)#', "$1\n\n", $result);
        $result = strip_tags($result);
        $result = preg_replace('#^ +#m', "", $result); //left trim whitespaces on each line
        $result = preg_replace('#([\n]){2,}#', "\n\n", $result); //limit newlines to 2 max
        $result = trim($result);
        return $result;
    }
}

class EmailDispatchmentType
{
    protected $displayUnsubscribeLink;
    protected $emailTemplate;
    protected $contentTemplate;
    protected $displayWebLink;
    protected $cssFiles;
    protected $projectCssFiles;
    protected $cssImagesURL;
    protected $theme;
    protected $linksTrackingEnabled = false;

    /**
     * @return boolean
     */
    public function isLinksTrackingEnabled()
    {
        return $this->linksTrackingEnabled;
    }

    /**
     * @var EmailDispatchmentRenderer
     */
    protected $emailDispatchmentRenderer;

    /**
     * @param EmailDispatchmentRenderer $emailDispatchmentRenderer
     */
    public function setEmailDispatchmentRenderer(EmailDispatchmentRenderer $emailDispatchmentRenderer)
    {
        $this->emailDispatchmentRenderer = $emailDispatchmentRenderer;
    }

    public function initialize()
    {
    }

    public function getContentTemplate()
    {
        return $this->contentTemplate;
    }

    public function getDisplayUnsubscribeLink()
    {
        return $this->displayUnsubscribeLink;
    }

    public function getDisplayWebLink()
    {
        return $this->displayWebLink;
    }

    public function getEmailTemplate()
    {
        return $this->emailTemplate;
    }

    public function getCssFiles()
    {
        $result = [];
        if (is_array($this->cssFiles)) {
            foreach ($this->cssFiles as &$path) {
                if (is_file($path)) {
                    $result[] = $path;
                }
            }
        }
        return $result;
    }

    public function getCssImagesURL()
    {
        return $this->cssImagesURL;
    }

    /**
     * please use getTrackedBlankImage instead of this
     * @deprecated
     */
    public function getTrackedImageUrl($filename)
    {
        $imageUrl = $this->getImageUrl($filename);
        $dispatchmentId = $this->emailDispatchmentRenderer->getDispatchmentHistoryId();
        if ($imageUrl && $dispatchmentId) {
            $parsedImageUrl = parse_url($imageUrl);
            $parsedImageUrl['path'] = '/emails/action:viewImage/id:' . $dispatchmentId . $parsedImageUrl['path'];
            $imageUrl = $this->emailDispatchmentRenderer->unparse_url($parsedImageUrl);
        }
        return $imageUrl;
    }

    public function getImageUrl($fileName)
    {
        $imageUrl = '';
        if ($theme = $this->getTheme()) {
            $imageUrl = $theme->getImageUrl($fileName);
        }
        return $imageUrl;
    }

    public function getTrackedBlankImage()
    {
        $controller = controller::getInstance();
        $dispatchmentId = $this->emailDispatchmentRenderer->getDispatchmentHistoryId();
        return $controller->baseURL . '/emails/action:viewBlankImage/id:' . $dispatchmentId . '/';
    }

    public function getTheme()
    {
        if (!$this->theme) {
            $designThemesManager = $this->emailDispatchmentRenderer->getDesignThemesManager();
            $this->theme = $designThemesManager->getCurrentTheme();
        }
        return $this->theme;
    }
}