<?php

class CurrencySelector implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $currenciesInformationList;
    protected $currenciesInformationIndex;
    /**
     * @var CurrencySelectorItem[]
     */
    protected $currencyObjectsList;
    /**
     * @var CurrencySelectorItem[]
     */
    protected $currencyObjectsIndex;
    protected $selectedCurrencyCode;

    /**
     * @param boolean $selectedCurrencyCode
     */
    public function setSelectedCurrencyCode($selectedCurrencyCode)
    {
        if ($currenciesInformationIndex = $this->getCurrenciesInformationIndex()) {
            if (isset($currenciesInformationIndex[strtolower($selectedCurrencyCode)])) {
                $this->selectedCurrencyCode = $selectedCurrencyCode;
                $_SESSION['selectedCurrencyCode'] = $this->selectedCurrencyCode;
                setcookie("selectedCurrencyCode", $this->selectedCurrencyCode, time() + 366 * 24 * 60 * 60, '/');
            }
        }
    }

    public function __construct()
    {

    }

    public function getCurrenciesList()
    {
        return $this->getCurrencyObjectsList();
    }

    public function convertPrice($price, bool $format = true)
    {
        $value = floatval(str_replace([" ", ','], ["", '.'], $price)) * $this->getSelectedCurrencyRate();

        if ($format) {
            return $this->formatPrice($value);
        }
        return $value;
    }

    public function formatPrice($price)
    {
        $formattedPrice = '0';
        $currentFormat = $this->getCurrentCurrencyFormat();
        $currentDecimals = (int)$currentFormat['decimals'];
        if ($currentFormat['decimals'] == 0) {
            $currentFormat['decimals'] = 2;
        }
        $stringPrice = number_format($price, $currentFormat['decimals'], $currentFormat['decPoint'], $currentFormat['thousandsSep']);
        $int = substr($stringPrice, 0, -$currentFormat['decimals'] - 1);
        $decimals = substr($stringPrice, -$currentFormat['decimals']);
        if ($currentDecimals === 0) {
            if ((int)$decimals === 0) {
                $formattedPrice = $int;
            } elseif ((int)$decimals >> 0) {
                $formattedPrice = $int . $currentFormat['decPoint'] . $decimals;
            }
        } else {
            $formattedPrice = $stringPrice;
        }
        return $formattedPrice;
    }

    public function getSelectedCurrencyCode()
    {
        if ($this->selectedCurrencyCode === null) {
            $this->detectSelectedCurrency();
        }
        return $this->selectedCurrencyCode;
    }

    public function getSelectedCurrencyItem()
    {
        $result = false;
        if ($currencyObjectsIndex = $this->getCurrencyObjectsIndex()) {
            if (isset($currencyObjectsIndex[$this->getSelectedCurrencyCode()])) {
                $result = $currencyObjectsIndex[$this->getSelectedCurrencyCode()];
            }
        }
        return $result;
    }

    public function getCurrentCurrencyFormat()
    {
        if ($currencyObjectsIndex = $this->getCurrencyObjectsIndex()) {
            if (isset($currencyObjectsIndex[$this->getSelectedCurrencyCode()])) {
                $result = $currencyObjectsIndex[$this->getSelectedCurrencyCode()];
                return $format = [
                    'decimals' => $result->decimals,
                    'decPoint' => $result->decPoint,
                    'thousandsSep' => $result->thousandsSep,
                ];
            }
        }
    }

    public function getDefaultCurrencyItem()
    {
        $result = false;
        $defaultCurrencyCode = false;
        if ($currenciesInformationList = $this->getCurrenciesInformationList()) {
            if ($firstCurrency = reset($currenciesInformationList)) {
                $defaultCurrencyCode = strtolower($firstCurrency['code']);
            }
        }
        if ($currencyObjectsIndex = $this->getCurrencyObjectsIndex()) {
            if (isset($currencyObjectsIndex[$defaultCurrencyCode])) {
                $result = $currencyObjectsIndex[$defaultCurrencyCode];
            }
        }
        return $result;
    }

    public function getSelectedCurrencyRate()
    {
        $rate = 1;
        if ($currencyObjectsIndex = $this->getCurrencyObjectsIndex()) {
            if (isset($currencyObjectsIndex[$this->selectedCurrencyCode])) {
                $rate = $currencyObjectsIndex[$this->selectedCurrencyCode]->rate;
            }
        }
        return $rate;
    }

    protected function getCurrenciesInformationList()
    {
        if ($this->currenciesInformationList === null) {
            $configManager = $this->getService('ConfigManager');
            $this->currenciesInformationList = (array)$configManager->get('currencies.list');
        }
        return $this->currenciesInformationList;
    }

    protected function getCurrenciesInformationIndex()
    {
        if ($this->currenciesInformationIndex === null) {
            $this->currenciesInformationIndex = [];
            if ($currenciesInformationList = $this->getCurrenciesInformationList()) {
                foreach ($currenciesInformationList as &$currencyInfo) {
                    $this->currenciesInformationIndex[strtolower($currencyInfo["code"])] = $currencyInfo;
                }
            }
        }
        return $this->currenciesInformationIndex;
    }

    protected function getCurrentURL()
    {
        $controller = $this->getService('controller');
        return $controller->pathURL;
    }

    protected function getCurrencyObjectsList()
    {
        if ($this->currencyObjectsList === null) {
            $this->currencyObjectsList = [];
            $this->currencyObjectsIndex = [];
            if ($currenciesInformationList = $this->getCurrenciesInformationList()) {
                $currentUrl = $this->getCurrentURL();
                foreach ($currenciesInformationList as $information) {
                    $currencyObject = new CurrencySelectorItem($information, $this->selectedCurrencyCode, $currentUrl);
                    $this->currencyObjectsList[] = $currencyObject;
                    $this->currencyObjectsIndex[$currencyObject->code] = $currencyObject;
                }
            }
        }
        return $this->currencyObjectsList;
    }

    protected function getCurrencyObjectsIndex()
    {
        if ($this->currencyObjectsIndex === null) {
            //currencyObjectsIndex is initialized through getCurrencyObjectsList
            $this->getCurrencyObjectsList();
        }
        return $this->currencyObjectsIndex;
    }

    protected function detectSelectedCurrency()
    {
        $controller = $this->getService('controller');
        if ($controller->getParameter('currency')) {
            if ($currenciesInformationIndex = $this->getCurrenciesInformationIndex()) {
                if (isset($currenciesInformationIndex[strtolower($controller->getParameter('currency'))])) {
                    $this->selectedCurrencyCode = strtolower($controller->getParameter('currency'));
                }
            }
        } elseif (isset($_SESSION['selectedCurrencyCode'])) {
            $this->selectedCurrencyCode = strtolower($_SESSION['selectedCurrencyCode']);
        } elseif (isset($_COOKIE['selectedCurrencyCode'])) {
            $this->selectedCurrencyCode = strtolower($_COOKIE['selectedCurrencyCode']);
        } else {
            if ($currenciesInformationList = $this->getCurrenciesInformationList()) {
                if ($firstCurrency = reset($currenciesInformationList)) {
                    $this->selectedCurrencyCode = strtolower($firstCurrency['code']);
                }
            }
        }
    }
}
