<?php

class settingsManager implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $settingsList;
    private static $instance;
    private $cachePath;
    private $fileName = 'settings.php';

    /**
     * @return settingsManager
     * @deprecated
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new settingsManager();
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::$instance = $this;
        $this->cachePath = $this->getService('PathsManager')->getPath('settingsCache');;
    }

    public function getSettingsList()
    {
        if (!isset($this->settingsList)) {
            $this->loadSettingsList();
        }

        return $this->settingsList;
    }

    public function getSetting($name)
    {
        if ($this->settingsList === null) {
            $this->loadSettingsList();
        }
        if (isset($this->settingsList[$name])) {
            return $this->settingsList[$name];
        }
        return false;
    }

    protected function loadSettingsList()
    {
        $settingsList = [];

        $filePath = $this->cachePath . $this->fileName;
        if (file_exists($filePath)) {
            include $filePath;
        } else {
            $this->generateSettingsFile();
        }
        $this->settingsList = $settingsList;
    }

    /**
     * Generate settings data, cache files
     */
    public function generateSettingsFile()
    {
        /**
         * @var [] $allData array
         * @var string $fileName settings file name
         */
        $allData = [];

        /**
         * Get data and push to $allData array
         */
        $db = $this->getService('db');
        $query = $db->table('module_simplesetting')
            ->leftJoin('structure_elements', 'module_simplesetting.id', '=', 'structure_elements.id')
            ->leftJoin('module_language', 'module_simplesetting.id', '=', 'module_language.id')
            ->select('structureName', 'value');

        if ($querySettings = $query->get()) {
            foreach ($querySettings as $setting) {
                $allData[$setting['structureName']] = $setting['value'];
            }
            $this->settingsList = $allData;
        }
        $this->getService('PathsManager')->ensureDirectory($this->cachePath);

        /**
         * Create cache files with settings data
         */
        $filePath = $this->cachePath . $this->fileName;
        $text = $this->generateSettingsText($allData);
        file_put_contents($filePath, $text);
    }

    protected function generateSettingsText($languageData)
    {
        $text = '<?php $settingsList = array(';
        foreach ($languageData as $name => &$value) {
            $text .= '"' . $name . '"=>"' . $value . '",';
        }
        $text .= '); ?>';

        return $text;
    }
}


