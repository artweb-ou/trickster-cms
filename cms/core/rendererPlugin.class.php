<?php

abstract class rendererPlugin extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $preferredEncodings = [];
    protected $renderingEngine;
    protected $requestHeadersManager;
    /**
     * @var CmsHttpResponse
     */
    protected $httpResponse;
    protected $maxAge;
    protected $lastModified;
    protected $cacheControl = 'public';
    protected $contentDisposition;
    protected $contentType;
    protected $contentText;
    protected $expires;
    protected $debugText = '';
    public $debugMode = false;

    abstract public function init();

    abstract public function assign($attributeName, $value);

    abstract public function fetch();

    abstract protected function renderContent();

    abstract protected function getContentType();

    abstract protected function getEtag();

    abstract protected function getContentLength();

    abstract protected function getContentTextPart();

    abstract protected function getContentDisposition();

    abstract protected function compress($encoding);

    final public function display()
    {
        $Etag = $this->getEtag();
        $this->httpResponse->setCacheControl($this->cacheControl);
        $this->encoding = $this->selectHTTPParameter($this->preferredEncodings, $this->requestHeadersManager->getAcceptedEncodings());

        if (!$this->checkEtag($Etag)) {
            $this->captureDebugText();
            $this->renderContent();

            $this->compress($this->encoding);

            $contentLength = $this->getContentLength();
            if (is_null($this->contentType)) {
                $contentType = $this->getContentType();
            } else {
                $contentType = $this->contentType;
            }

            if ($fileName = $this->getFileName()) {
                $this->httpResponse->setFileName($fileName);
            }

            $this->httpResponse->setLastModified($this->lastModified);
            $this->httpResponse->setMaxAge($this->maxAge);
            $this->httpResponse->setExpires($this->expires);
            $this->httpResponse->setEtag($Etag);
            $this->httpResponse->setContentDisposition($this->getContentDisposition());
            $this->httpResponse->setContentEncoding($this->encoding);
            $this->httpResponse->setContentLength($contentLength);
            $this->httpResponse->setContentType($contentType);
            $this->endOutputBuffering();

            $this->httpResponse->sendHeaders();
            if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'HEAD') {
                while ($contentText = $this->getContentTextPart()) {
                    $this->httpResponse->sendContent($contentText);
                }
            }
        } else {
            $this->httpResponse->setStatusCode('304');
            $this->httpResponse->setMaxAge($this->maxAge);
            $this->httpResponse->setExpires($this->expires);
            $this->httpResponse->setEtag($Etag);
            $this->endOutputBuffering();
            $this->httpResponse->sendHeaders();
        }
    }

    final protected function checkEtag($currentEtag)
    {
        $requestedEtag = $this->requestHeadersManager->getIfNoneMatch();
        if ($requestedEtag == '"' . $currentEtag . '"') {
            return true;
        } else {
            return false;
        }
    }

    final protected function captureDebugText()
    {
        $errorLog = errorLog::getInstance();
        $errorLogMessages = $errorLog->getAllMessages();

        $errorText = '';
        foreach ($errorLogMessages as &$message) {
            $errorText .= '<br/><b>' . $message['locationName'] . ':</b> ' . $message['errorText'];
        }

        $this->debugText = ob_get_contents() . $errorText;
        if (strlen($this->debugText) > 0) {
            $this->debugText = '<div class="debug_block">' . $this->debugText . "</div>";
        }
    }

    final public function fileNotFound()
    {
        $this->httpResponse->setStatusCode('404');
        $this->endOutputBuffering();

        $this->httpResponse->sendHeaders();
    }

    final protected function selectHTTPParameter($preferredOrder, $HTTPParameters, $universalParameter = null)
    {
        $preferredParameter = false;

        $oneLevelParameters = [];
        arsort($HTTPParameters);
        $parametersCount = count($HTTPParameters);
        foreach ($HTTPParameters as $parameter => &$level) {
            $parametersCount--;
            if ($parameter == $universalParameter) {
                return reset($preferredOrder);
            }
            if (reset($oneLevelParameters) > $level) {
                foreach ($preferredOrder as &$preferredParameter) {
                    if (isset($oneLevelParameters[$preferredParameter])) {
                        return $preferredParameter;
                    }
                }
                $oneLevelParameters = [];
            }
            $oneLevelParameters[$parameter] = $level;
            if ($parametersCount == 0) {
                foreach ($preferredOrder as &$preferredParameter) {
                    if (isset($oneLevelParameters[$preferredParameter])) {
                        return $preferredParameter;
                    }
                }
            }
        }
        return $preferredParameter;
    }

    public function getFileName()
    {
        return false;
    }

    final protected function gzip($contentText)
    {
        return gzencode($contentText, 3);
    }

    public function setContentDisposition($value)
    {
        $this->contentDisposition = $value;
    }

    public function setMaxAge($value)
    {
        $this->maxAge = $value;
    }

    public function setLastModified($value)
    {
        $this->lastModified = $value;
    }

    public function setCacheControl($value)
    {
        $this->cacheControl = $value;
    }

    public function setContentType($value)
    {
        $this->contentType = $value;
    }

    public function endOutputBuffering()
    {
        //todo: remove workaround and provide proper ob handler
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    public function getAttribute($attributeName)
    {
        return false;
    }
}

