<?php

class redirectionManager implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    /** @var redirectionManager */
    private static $instance = false;
    protected $redirectForced;

    public function __construct()
    {
        self::$instance = $this;
        if (isset($_SESSION['redirectForced'])) {
            $this->redirectForced = $_SESSION['redirectForced'];
        }
    }

    /**
     * @deprecated
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new redirectionManager();
        }
        return self::$instance;
    }

    public function checkRedirection()
    {
        if ($uri = $this->checkProtocolRedirection()) {
            $this->redirect($uri, 301);
        }
        if ($uri = $this->checkDomainRedirection()) {
            $this->redirect($uri, 301);
        }
    }

    public function forceRedirect($type)
    {
        $_SESSION['redirectForced'] = $type;
        $this->redirectForced = $type;
    }

    public function redirectToElement($elementId, $languageCode = '')
    {
        if (!$languageCode) {
            $languageCode = $this->getService('languagesManager')->getCurrentLanguageCode();
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
                return $controller->fullURL;
            }
        }

        return false;
    }

    public function checkDomainRedirection()
    {
        if ($domainRedirections = $this->getDomainRedirections()) {
            $controller = $this->getService('controller');
            foreach ($domainRedirections as $domain => $url) {
                if (stripos($controller->domainName, $domain) !== false) {
                    return $url;
                }
            }
        }
        return false;
    }

    public function getDomainRedirections()
    {
        $result = $this->getService('ConfigManager')->get('domains.redirections');
        if (!$result && isset($GLOBALS['config_domains']['redirections'])) {
            // deprecated since 2016.03
            $result = $GLOBALS['config_domains']['redirections'];
        }
        return $result;
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