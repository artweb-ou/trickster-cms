<?php

class ShoppingBasketDeliveryTargets implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    /**
     * @var ShoppingBasketDeliveryTargets
     */
    protected static $instance = null;
    /**
     * @var ShoppingBasketCountry[]
     */
    protected $countriesList = [];
    /**
     * @var ShoppingBasketCountry[]
     */
    protected $countriesIndex = [];
    /**
     * @var ShoppingBasketCity
     */
    protected $citiesIndex = [];
    protected $selectedCountryId = false;
    public $selectedCityId = false;
    protected $countriesData;

    /**
     * @return ShoppingBasketDeliveryTargets
     * @deprecated
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::$instance = $this;
        $this->loadStorage();
        $this->recalculate();
    }

    protected function saveStorage()
    {
        $languagesManager = $this->getService('languagesManager');;
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        $data = [];
        $data['countriesData'] = $this->countriesData;
        $data['languageId'] = $currentLanguageId;
        $data['selectedCountryId'] = $this->selectedCountryId;
        $data['selectedCityId'] = $this->selectedCityId;

        $user = $this->getService('user');
        $user->setStorageAttribute('deliveryTargetsData', $data);
    }

    protected function loadStorage()
    {
        $user = $this->getService('user');

        $languagesManager = $this->getService('languagesManager');;
        $currentLanguageId = $languagesManager->getCurrentLanguageId();

        if (!($data = $user->getStorageAttribute('deliveryTargetsData')) || $data['languageId'] != $currentLanguageId) {
            $this->countriesData = $this->loadCountriesData();

            $data = [];
            $data['countriesData'] = $this->countriesData;
            $data['selectedCountryId'] = null;
            $data['selectedCityId'] = null;
        }

        $this->selectedCountryId = $data['selectedCountryId'];
        $this->selectedCityId = $data['selectedCityId'];
        $this->countriesData = $data['countriesData'];

        foreach ($this->countriesData as &$storageData) {
            $country = new ShoppingBasketCountry($storageData);
            $this->countriesList[] = $country;
            $this->countriesIndex[$country->id] = $country;
            foreach ($storageData['cities'] as &$cityData) {
                $city = new ShoppingBasketCity($cityData);
                $this->citiesIndex[$city->id] = $city;
            }
        }
    }

    protected function loadCountriesData()
    {
        $data = [];

        $structureManager = $this->getService('structureManager');
        $linksManager = $this->getService('linksManager');
        if ($countriesElementId = $structureManager->getElementIdByMarker('deliveryCountries')) {
            $connectedIds = $linksManager->getConnectedIdList($countriesElementId, 'structure', 'parent');
            $countryElements = $structureManager->getElementsByIdList($connectedIds, null, true);
            foreach ($countryElements as &$countryElement) {
                $elementData = [];
                $elementData['id'] = $countryElement->id;
                $elementData['title'] = $countryElement->title;
                $elementData['conditionsText'] = $countryElement->conditionsText;
                $elementData['iso3166_1a2'] = $countryElement->iso3166_1a2;
                $elementData['cities'] = [];
                /**
                 * @var deliveryCityElement[] $cities
                 */
                if ($cities = $structureManager->getElementsChildren($countryElement->id)) {
                    foreach ($cities as &$cityElement) {
                        $cityData = [];
                        $cityData['id'] = $cityElement->id;
                        $cityData['title'] = $cityElement->title;

                        $elementData['cities'][] = $cityData;
                    }
                }
                $data[] = $elementData;
            }
        }
        return $data;
    }

    protected function recalculate()
    {
        //some delivery target should always be selected
        if (!$this->selectedCountryId) {
            if ($country = reset($this->countriesList)) {
                $this->selectedCountryId = $country->id;

                $citiesList = $country->getActiveCitiesList();
                if (count($citiesList) > 0) {
                    $city = reset($citiesList);
                    $this->selectedCityId = $city->id;
                }

                $this->setSelectedDeliveryCountryId($this->selectedCountryId);
                if (count($country->citiesList) > 0) {
                    $city = reset($country->citiesList);
                    $this->setSelectedDeliveryCityId($city->id);
                }
            }
            $this->saveStorage();
        } elseif (!$this->selectedCityId) {
            $citiesList = $this->countriesIndex[$this->selectedCountryId]->getActiveCitiesList();
            if (count($citiesList) > 0) {
                $city = reset($citiesList);
                $this->selectedCityId = $city->id;
            }
            $this->saveStorage();
        }
    }

    public function checkDeliveryCountry($targetsIdList, $countryId = null)
    {
        if (is_null($countryId)) {
            $countryId = $this->selectedCountryId;
        }
        $result = false;
        foreach ($targetsIdList as &$targetId) {
            if ($countryId == $targetId || isset($this->countriesIndex[$countryId]->citiesIndex[$targetId])) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function setSelectedDeliveryCityId($targetId)
    {
        //update selected city
        foreach ($this->countriesList as &$country) {
            foreach ($country->citiesList as &$city) {
                if ($city->id == $targetId) {
                    $this->selectedCountryId = $country->id;
                    $this->selectedCityId = $city->id;
                    break;
                }
            }
        }
        $this->saveStorage();
        $shoppingBasketDeliveryTypes = $this->getService('shoppingBasketDeliveryTypes');
        $shoppingBasketDeliveryTypes->resetDeliveryType();
    }

    public function setSelectedDeliveryCountryId($countryId)
    {
        if (isset($this->countriesIndex[$countryId])) {
            $this->selectedCountryId = $countryId;
            $this->selectedCityId = null;
            $citiesList = $this->countriesIndex[$countryId]->getActiveCitiesList();
            if (count($citiesList) > 0) {
                $city = reset($citiesList);
                $this->selectedCityId = $city->id;
            }
        }
        $this->saveStorage();
    }

    public function resetDeliveryCity()
    {
        $this->selectedCityId = null;
        $this->recalculate();
    }

    public function getActiveCountriesList()
    {
        $result = [];

        $shoppingBasketDeliveryTypes = $this->getService('shoppingBasketDeliveryTypes');
        $deliveryTypesList = $shoppingBasketDeliveryTypes->getDeliveryTypesList();
        foreach ($this->countriesList as &$country) {
            foreach ($deliveryTypesList as &$deliveryType) {
                if ($this->checkDeliveryCountry($deliveryType->deliveryTargetsIdList, $country->id)) {
                    $result[] = $country;
                    break;
                }
            }
        }
        return $result;
    }

    public function getSelectedCountryId()
    {
        $this->recalculate();
        return $this->selectedCountryId;
    }

    public function getSelectedCityId()
    {
        $this->recalculate();
        return $this->selectedCityId;
    }

    public function getSelectedDeliveryTargetId()
    {
        $this->recalculate();
        $result = false;
        if ($this->selectedCityId) {
            $result = $this->selectedCityId;
        } elseif ($this->selectedCountryId) {
            $result = $this->selectedCountryId;
        }
        return $result;
    }

    public function getCountry($id)
    {
        if (isset($this->countriesIndex[$id])) {
            return $this->countriesIndex[$id];
        }
        return false;
    }

    public function getCity($id) {
        if (isset($this->citiesIndex[$id])) {
            return $this->citiesIndex[$id];
        }
        return false;
    }
}
