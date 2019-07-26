<?php

class RedirectionManager implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    public function redirectToElement($elementId, $languageCode = '')
    {
        if (!$languageCode) {
            $languageCode = $this->getService('LanguagesManager')->getCurrentLanguageCode();
        }
        $configManager = $this->getService('ConfigManager');
        $structureManager = $this->getService('structureManager', [
            'rootMarker' => $configManager->get('main.rootMarkerPublic'),
            'requestedPath' => [$languageCode],
        ], true);
        $element = $structureManager->getElementById($elementId);
        $this->redirect($element->URL);
    }

    public function switchLanguage($newLanguageCode, $referrerElementId = 0, $application = '')
    {
        $uriSwitchLogics = $this->getService('uriSwitchLogics');
        $uriSwitchLogics->setLanguageCode($newLanguageCode);
        $uriSwitchLogics->setApplication($application);

        $this->redirect($uriSwitchLogics->findForeignRelativeUrl($referrerElementId), '301');
    }

    public function redirectToMobile($uri, $uriType = 'desktop', $newLanguage = null)
    {
        $uriSwitchLogics = $this->getService('uriSwitchLogics');
        if ($uriType == 'mobile') {
            $uriSwitchLogics->setCurrentMobileUri($uri);
        } else {
            $uriSwitchLogics->setCurrentUri($uri);
        }

        if (!is_null($newLanguage)) {
            $uriSwitchLogics->setLanguage($newLanguage);
        }

        $result = $uriSwitchLogics->getMobileUri();
        $this->redirect($result, '302');
    }

    public function redirectToPublic($uri, $newLanguage = null)
    {
        $uriSwitchLogics = $this->getService('uriSwitchLogics');
        $uriSwitchLogics->setCurrentUri($uri);

        if (!is_null($newLanguage)) {
            $uriSwitchLogics->setLanguage($newLanguage);
        }

        $result = $uriSwitchLogics->getDesktopUri();
        $this->redirect($result, '302');
    }

    public function redirectToDesktop($uri = null)
    {
        $uriSwitchLogics = $this->getService('uriSwitchLogics');
        $uriSwitchLogics->setCurrentMobileUri($uri);

        $result = $uriSwitchLogics->getDesktopUri();
        $this->redirect($result, '302');
    }

    public function redirect($uri, $status = 302)
    {
        if ($status == 301) {
            header("HTTP/1.1 301 Moved Permanently");
        } elseif ($status == 302) {
            header("HTTP/1.1 302 Found");
        } elseif ($status == 303) {
            header("HTTP/1.1 303 See Other");
        } elseif ($status == 404) {
            header("HTTP/1.0 404 Not Found");
        }
        header('Location: ' . $uri);
        exit;
    }

    public function checkRedirectionUrl($errorUrl)
    {
        if ($redirectUrl = $this->getRedirectionUrl($errorUrl)) {
            $this->redirect($redirectUrl, '301');
        } elseif ($redirectUrl = $this->getBestGuessRedirectionUrl($errorUrl)) {
            $this->redirect($redirectUrl, '301');
        }
        return false;
    }

    public function getRedirectionUrl($errorUrl)
    {
        $redirectUrl = "";
        /**
         * @var \Illuminate\Database\MySqlConnection $db
         */
        $db = $this->getService('db');
        $query = $db->table('module_redirect')
            ->where('sourceUrl', '=', $errorUrl)
            ->where('partialMatch', '=', 0)
            ->limit(1);

        $relevantRecord = $query->first();

        if (!$relevantRecord) {
            $query = $db->table('module_redirect')
                ->whereRaw('? LIKE CONCAT("%", sourceUrl, "%")', [$errorUrl])
                ->where('partialMatch', '=', 1)
                ->limit(1);
            $relevantRecord = $query->first();
        }

        if ($relevantRecord) {
            if ($relevantRecord["destinationElementId"]) {
                $structureManager = $this->getService('structureManager', [
                    'rootMarker' => $this->getService('ConfigManager')->get('main.rootMarkerPublic'),
                ], true);

                if ($redirectElement = $structureManager->getElementById($relevantRecord["destinationElementId"])) {
                    $redirectUrl = $redirectElement->URL;
                }
            } elseif ($relevantRecord["destinationUrl"]) {
                if ($relevantRecord['partialMatch']) {
                    $redirectUrl = str_ireplace($relevantRecord["sourceUrl"], $relevantRecord["destinationUrl"],
                        $errorUrl);
                } else {
                    $redirectUrl = $relevantRecord["destinationUrl"];
                }
            }
        }
        return $redirectUrl;
    }

    public function checkProtocolRedirection()
    {
        $configProtocol = $this->getService('ConfigManager')->get('main.protocol');

        if ($configProtocol) {
            $controller = $this->getService('controller');
            $currentProtocol = $controller->getProtocol();
            if ($currentProtocol != $configProtocol) {
                $this->redirect($controller->fullURL, 301);
            }
        }

        return false;
    }

    public function checkDomainRedirection()
    {
        if ($domainRedirections = $this->getService('ConfigManager')->get('domains.redirections')) {
            $controller = $this->getService('controller');
            foreach ($domainRedirections as $domain => $url) {
                if (stripos($controller->domainName, $domain) !== false) {
                    $this->redirect($url, 301);
                }
            }
        }
        return false;
    }

    protected function getBestGuessRedirectionUrl($errorUrl)
    {
        if ($urlInfo = parse_url($errorUrl)) {
            if (!empty($urlInfo['path'])) {
                if ($urlParts = explode('/', $urlInfo['path'])) {
                    foreach ($urlParts as $key => &$part) {
                        if (trim($part) == '') {
                            unset($urlParts[$key]);
                        }
                    }
                    if (count($urlParts) > 1) {
                        $db = $this->getService('db');
                        $structureManager = $this->getService('structureManager');
                        if ($rows = $db->table('structure_elements')
                            ->select('id')
                            ->where('structureName', 'like', '%' . last($urlParts) . '%')
                            ->limit(1)
                            ->get()
                        ) {
                            $row = reset($rows);
                            if ($element = $structureManager->getElementById($row['id'])) {
                                return $element->URL;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}