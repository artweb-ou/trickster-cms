<?php

class imageProcessRendererPlugin extends rendererPlugin
{
    protected $exportOperation = null;
    protected $contentRead = false;
    protected $cachePath = false;

    public function init()
    {
        $configManager = $this->getService('ConfigManager');
        $this->requestHeadersManager = $this->getService('requestHeadersManager');
        $this->httpResponse = CmsHttpResponse::getInstance();

        $pathsManager = $this->getService('PathsManager');
        $this->cachePath = $pathsManager->getPath('imagesCache');
        $this->renderingEngine = new \ImageProcess\ImageProcess($this->cachePath);
        $defaultCachePermissions = $configManager->get('paths.defaultCachePermissions');
        $this->renderingEngine->setDefaultCachePermissions($defaultCachePermissions);
        $this->maxAge = 365 * 60 * 60 * 24;
        $this->httpResponse->setCacheControl('public');
        $this->preferredEncodings = ['identity'];
    }

    public function __destruct()
    {
        if (class_exists("cachePurge")) {
            new cachePurge($this->cachePath, 300, 2592000, 200);
        }
    }

    public function fetch()
    {
    }

    public function assign($attributeName, $value)
    {
        if (method_exists($this->renderingEngine, $attributeName)) {
            if (!isset($value[0])) {
                $value[0] = null;
            }
            if (!isset($value[1])) {
                $value[1] = null;
            }
            if (!isset($value[2])) {
                $value[2] = null;
            }
            if (!isset($value[3])) {
                $value[3] = null;
            }
            if (!isset($value[4])) {
                $value[4] = null;
            }
            $result = $this->renderingEngine->$attributeName($value[0], $value[1], $value[2], $value[3], $value[4]);
            if ($attributeName == 'registerExport') {
                $this->exportOperation = $result;
            }
        }
    }

    protected function getEtag()
    {
        $exportHash = $this->exportOperation['parametersHash'];
        return $exportHash;
    }

    protected function getContentLength()
    {
        $imageFilePath = $this->exportOperation['cacheFilePath'];
        if (is_file($imageFilePath)) {
            return filesize($imageFilePath);
        }
        return 0;
    }

    protected function getContentType()
    {
        $contentTypes = $this->requestHeadersManager->getAcceptedTypes();
        $imageType = $this->exportOperation['fileType'];
        $preferredOrder = false;
        if ($imageType == 'webp') {
            $preferredOrder = ['image/webp'];
        } elseif ($imageType == 'png') {
            $preferredOrder = ['image/png'];
        } elseif ($imageType == 'gif') {
            $preferredOrder = ['image/gif'];
        } elseif ($imageType == 'jpg' || $imageType == 'jpeg') {
            $preferredOrder = ['image/jpeg'];
        } elseif ($imageType == 'bmp') {
            $preferredOrder = ['image/x-bmp'];
        } elseif ($imageType == 'svg') {
            $preferredOrder = ['image/svg+xml'];
        }

        $selectedType = $this->selectHTTPParameter($preferredOrder, $contentTypes, '*/*');

        return $selectedType;
    }

    public function getContentDisposition()
    {
        return $this->contentDisposition ?: 'inline';
    }

    protected function renderContent()
    {
        $this->renderingEngine->executeProcess();
    }

    protected function getContentTextPart()
    {
        if (!$this->contentRead) {
            $this->contentRead = true;
            $imageFilePath = $this->exportOperation['cacheFilePath'];
            if (is_file($imageFilePath)){
                return file_get_contents($imageFilePath);
            }
        }
        return false;
    }

    protected function compress($encoding)
    {
    }
}