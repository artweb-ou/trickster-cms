<?php

class languagesManager extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $currentLanguageInfo = null;
    protected $languagesList = [];
    protected $languagesIdList = [];
    protected $map = [];
    protected $ciMap = [];
    protected $shortToLongCodes = [
        'et' => 'est',
        'ru' => 'rus',
        'en' => 'eng',
        'lv' => 'lat',
        'lt' => 'lit',
        'fi' => 'fin',
        'be' => 'bel',
    ];
    /** @var languagesManager */
    public static $instance = false;

    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * @return languagesManager
     * @deprecated
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new languagesManager();
        }
        return self::$instance;
    }

    public static function resetInstance()
    {
        self::$instance = null;
    }

    public function reset()
    {
        $this->currentLanguageInfo = null;
        $this->languagesList = [];
        $this->languagesIdList = [];
        $this->map = [];
        $this->ciMap = [];
    }

    public function getCurrentLanguageCode($groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if (!isset($this->currentLanguageInfo[$groupName])) {
            $this->detectCurrentLanguageCode($groupName);
        }
        return $this->currentLanguageInfo[$groupName]->iso6393;
    }

    public function getDefaultLanguageCode($groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if ($defaultLanguage = $this->getDefaultLanguage($groupName)) {
            return $defaultLanguage->iso6393;
        }
        return false;
    }

    public function getCurrentLanguageId($groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if (!isset($this->currentLanguageInfo[$groupName])) {
            $this->detectCurrentLanguageCode($groupName);
        }
        if (isset($this->currentLanguageInfo[$groupName]) && is_object($this->currentLanguageInfo[$groupName])) {
            return $this->currentLanguageInfo[$groupName]->id;
        }
        return false;
    }

    public function getLanguagesList($groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if (!isset($this->languagesList[$groupName])) {
            $collection = persistableCollection::getInstance('module_language');
            $this->languagesList[$groupName] = $collection->load(['group' => $groupName]);
            if ($this->languagesList[$groupName]) {
                $this->sortLanguages($this->languagesList[$groupName]);
            }
        }

        return $this->languagesList[$groupName];
    }

    public function getLanguagesIdList($groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if (!isset($this->languagesIdList[$groupName])) {
            $languagesIdList = [];
            $info = $this->getLanguagesList($groupName);
            foreach ($info as &$language) {
                $languagesIdList[] = $language->id;
            }
            $this->languagesIdList[$groupName] = $languagesIdList;
        }

        return $this->languagesIdList[$groupName];
    }

    /**
     * returns language that is used when a new client enters the site and
     * there is no language in the URL nor is it guessable in any way
     * @param $groupName
     * @return object
     */
    public function getDefaultLanguage($groupName)
    {
        $result = null;
        if ($languageCode = $this->getCodeFromConfig()) {
            $map = $this->getLanguagesCiMap($groupName);
            $result = isset($map[$languageCode]) ? $map[$languageCode] : null;
        }
        if (!$result) {
            $result = $this->findFirstAvailableLanguage($groupName);
        }
        return $result;
    }

    protected function detectCurrentLanguageCode($groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        $languageCode = $this->getCodeFromURI();
        if ($this->checkLanguageCode($languageCode, $groupName)) {
            goto finish;
        }
        $languageCode = $this->getCodeFromSession($groupName);
        if ($this->checkLanguageCode($languageCode, $groupName)) {
            goto finish;
        }
        $languageCode = $this->getCodeFromCookies($groupName);
        if ($this->checkLanguageCode($languageCode, $groupName, false)) {
            goto finish;
        }
        $languageCode = $this->getCodeFromConfig();
        if ($this->checkLanguageCode($languageCode, $groupName, false)) {
            goto finish;
        }
        $headerLanguages = $this->parseLanguagesFromAcceptHeader();
        foreach ($headerLanguages as $languageCode) {
            if (strlen($languageCode) === 2) {
                if (!isset($this->shortToLongCodes[$languageCode])) {
                    continue;
                }
                $languageCode = $this->shortToLongCodes[$languageCode];
            }
            if ($this->checkLanguageCode($languageCode, $groupName, false)) {
                goto finish;
            }
        }
        finish:
        if ($languageCode) {
            $this->setCurrentLanguageCode($languageCode, $groupName);
        }
    }

    /**
     * Parses Accept-Language header, returns list of langs ordered by importance descending
     * Header example: "en-US,en;q=0.8,et;q=0.6,ru;q=0.4"
     * @link https://tools.ietf.org/html/rfc7231#section-5.3.5
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
     * @return string[]
     */
    protected function parseLanguagesFromAcceptHeader()
    {
        $header = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            ? (string)$_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $header = str_replace(' ', '', $header);
        if ($header === '' || $header === '*') {
            return [];
        }
        $parts = explode(',', strtolower($header));
        $languages = [];
        $qualities = [];
        foreach ($parts as $part) {
            $lang = trim($part);
            $q = 1;
            $i = strpos($lang, ';q=');
            if ($i !== false) {
                $q = (float)trim(substr($lang, $i + 3));
                $lang = trim(substr($lang, 0, $i));
            }
            $i = strpos($lang, '-');
            $lang = $i === false ? $lang : trim(substr($lang, 0, $i));
            if ($lang === '') {
                continue;
            }
            $qualities[] = $q;
            $languages[] = $lang;
        }
        array_multisort($qualities, SORT_DESC, $languages);
        $languages = array_unique($languages);
        return $languages;
    }

    protected function getCodeFromCookies($groupName)
    {
        $code = false;
        if (isset($_COOKIE['cl_' . $groupName])) {
            $code = $_COOKIE['cl_' . $groupName];
        } elseif (isset($_COOKIE['currentLanguage' . $groupName])) {
            //deprecated
            //todo: remove in 2020
            $code = $_COOKIE['currentLanguage' . $groupName];
            setcookie('currentLanguage' . $groupName, '', -1, '/');
        }
        return $code;
    }

    protected function getCodeFromSession($groupName)
    {
        $code = false;
        if (isset($_SESSION['currentLanguage' . $groupName])) {
            $code = $_SESSION['currentLanguage' . $groupName];
        }

        return $code;
    }

    protected function getCodeFromURI()
    {
        $controller = $this->getService('controller');
        $code = false;

        if (count($controller->requestedPath) > 0) {
            $code = reset($controller->requestedPath);
        }

        return $code;
    }

    protected function findFirstAvailableLanguage($groupName)
    {
        $result = null;
        $languagesList = $this->getLanguagesList($groupName) ?: [];
        foreach ($languagesList as &$language) {
            if (!$language->hidden) {
                $result = $language;
                break;
            }
        }
        if (!$result && $languagesList) {
            $result = $languagesList[0];
        }
        return $result;
    }

    protected function getCodeFromConfig()
    {
        return $this->getService('ConfigManager')->get('languages.default');
    }

    public function checkLanguageCode($code, $groupName, $checkHidden = true)
    {
        $result = false;
        if ($code) {
            $map = $this->getLanguagesCiMap($groupName);
            $code = strtolower($code);
            $result = isset($map[$code]) && ($checkHidden || !$map[$code]->hidden)
                ? $map[$code] : false;
        }
        return $result;
    }

    public function setCurrentLanguageCode($code, $groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if (!isset($this->currentLanguageInfo[$groupName]) || $this->currentLanguageInfo[$groupName]->iso6393 != $code) {
            if ($info = $this->checkLanguageCode($code, $groupName)) {
                $this->currentLanguageInfo[$groupName] = $info;
                $_SESSION['currentLanguage' . $groupName] = $code;
                setcookie('cl_' . $groupName, $code, time() + 30 * 24 * 60 * 60, '/');
            }
        }
    }

    public function getLanguagesMap($groupName = '')
    {
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if (!isset($this->map[$groupName])) {
            $this->map[$groupName] = [];
            foreach ($this->getLanguagesList($groupName) as $language) {
                $this->map[$groupName][$language->iso6393] = $language;
            }
        }
        return $this->map[$groupName];
    }

    protected function getLanguagesCiMap($groupName = '')
    {
        // TODO: replace getLanguagesMap?
        $groupName = $groupName ?: $this->getService('ConfigManager')
            ->get('main.rootMarkerPublic');
        if (!isset($this->ciMap[$groupName])) {
            $this->ciMap[$groupName] = [];
            foreach ($this->getLanguagesList($groupName) as $language) {
                $this->ciMap[$groupName][strtolower($language->iso6393)] = $language;
            }
        }
        return $this->ciMap[$groupName];
    }

    protected function sortLanguages(array &$languages)
    {
        if ($languages) {
            $languagesIds = [];
            foreach ($languages as $language) {
                $languagesIds[] = $language->id;
            }
            $positionsMap = [];
            $collection = persistableCollection::getInstance('structure_links');
            $conditions = [
                [
                    'column' => 'childStructureId',
                    'action' => 'IN',
                    'argument' => $languagesIds,
                ],
            ];
            if ($rows = $collection->conditionalLoad([
                'childStructureId',
                'position',
            ], $conditions)
            ) {
                foreach ($rows as &$row) {
                    $positionsMap[$row['childStructureId']] = $row['position'];
                }
            }
            $positions = [];
            foreach ($languages as $language) {
                $positions[] = isset($positionsMap[$language->id]) ? $positionsMap[$language->id] : 0;
            }
            array_multisort($positions, SORT_ASC, $languages);
        }
    }

    public function getCurrentLanguageElement()
    {
        $structureManager = $this->getService('structureManager');
        return $structureManager->getElementById($this->getCurrentLanguageId());
    }
}

