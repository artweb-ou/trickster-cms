<?php

class DesignThemesManager
{
    protected $currentThemeCode;
    protected $themesDirectoryPathList = [];
    protected $themesIndex = [];

    public function __construct()
    {
    }

    /**
     * Returns a design theme by code
     * @param string $code
     * @return DesignTheme|bool
     */
    public function getTheme($code)
    {
        if (!isset($this->themesIndex[$code])) {
            $this->themesIndex[$code] = $this->manufactureTheme($code);
        }

        return $this->themesIndex[$code];
    }

    /**
     * Returns a current design theme by code
     * @return DesignTheme|bool
     */
    public function getCurrentTheme()
    {
        return $this->getTheme($this->getCurrentThemeCode());
    }

    public function getCurrentThemeCode()
    {
        if (is_null($this->currentThemeCode)) {
            if (isset($_SESSION['DesignTheme'])) {
                if ($this->getTheme($_SESSION['DesignTheme'])) {
                    $this->currentThemeCode = $_SESSION['DesignTheme'];
                }
            }
        }
        return $this->currentThemeCode;
    }

    /**
     * Sets the current theme by code and controls if it's accessible
     * @param string $currentThemeCode
     * @return bool|\DesignTheme
     */
    public function setCurrentThemeCode($currentThemeCode)
    {
        if ($theme = $this->getTheme($currentThemeCode)) {
            $_SESSION['DesignTheme'] = $currentThemeCode;
            $this->currentThemeCode = $currentThemeCode;
        }
        return $theme;
    }

    /**
     * @param string $themesDirectoryPath
     */
    public function setThemesDirectoryPath($themesDirectoryPath)
    {
        $this->themesDirectoryPathList[] = $themesDirectoryPath;
    }

    /**
     * Manufactures and returns new theme object
     * @param string $code
     * @return DesignTheme|bool
     */
    protected function manufactureTheme($code)
    {
        $result = false;
        if (!$code !== null) {
            $className = $code . 'DesignTheme';
            foreach ($this->themesDirectoryPathList as $themesDirectoryPath) {
                if (!class_exists($className, false)) {
                    $filePath = $themesDirectoryPath . $code . '.class.php';
                    if (is_file($filePath)) {
                        include_once($filePath);
                    }
                }
                if (class_exists($className, false)) {
                    $result = new $className($this, $code);
                    break;
                }
            }
        }
        return $result;
    }
}
