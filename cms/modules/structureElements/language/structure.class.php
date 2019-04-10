<?php

/**
 * Class languageElement
 *
 * @property string $title
 * @property string $iso6393
 * @property string $logoImage
 * @property string $logoImageOriginalName
 */
class languageElement extends structureElement implements MetadataProviderInterface
{
    use MetadataProviderTrait;
    public $dataResourceName = 'module_language';
    protected $allowedTypes = [
        'folder',
        'shoppingBasket',
        'productCatalogue',
        'brandsList',
        'discountsList',
        'campaignsList',
        'bannerCategory',
        'newsList',
        'eventsList',
        'service',
        'production',
        'shopCatalogue',
    ];
    protected $allowedTypesColumns = [
        'article',
        'newsMailForm',
        'subMenuList',
        'search',
        'selectedProducts',
        'currencySelector',
        'pollPlaceholder',
        'widget',
        'login',
        'latestNews',
        'shoppingBasketStatus',
        'bannerCategory',
        'personnel',
        'selectedEvents',
        'productSearch',
        'selectedDiscounts',
        'floorPlanControls',
        'shopCatalogueControls',
    ];
    protected $allowedTypesHeader = [
        'currencySelector',
        'article',
        'search',
        'gallery',
        'brandsWidget',
        'shoppingBasketStatus',
        'bannerCategory',
        'login',
        'subMenuList',
        'selectedProducts',
        'selectedEvents',
        'selectedDiscounts',
        'linkList',
        'openingHoursInfo',
        'productGallery',
        'productSearch',
    ];
    protected $allowedTypesFooter = [
        'folder',
        'latestNews',
        'newsMailForm',
        'article',
        'subMenuList',
        'brandsWidget',
        'bannerCategory',
        'selectedEvents',
        'widget',
        'map',
    ];
    protected $allowedTypesMobile = [
        'article',
        'subMenuList',
    ];
    public $defaultActionName = 'show';
    public $role = 'container';
    protected $leftColumnElementsList;
    protected $rightColumnElementsList;
    protected $headerElementsList;
    protected $headerElementsIndex;
    protected $footerElementsList;
    protected $footerElementsIndex;
    protected $mobileMenuElementsList;
    protected $mobileMenuElementsIndex;
    protected $elementsIndex;
    protected $elementsList;
    protected $currentMainMenu;
    protected $mainMenuElements;
    protected $firstPageElement;

    protected function getTabsList()
    {
        return [
            'showFullList',
            'showForm',
            'headerContent',
            'leftColumn',
            'rightColumn',
            'bottomMenu',
            'mobileMenu',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getMainMenuElements($ignoreHidden = false)
    {
        if ($ignoreHidden) {
            $structureManager = $this->getService('structureManager');
            //TODO: remove hack required for catalogue filters
            $structureManager->getElementsChildren($this->id, 'container');
            if ($childElements = $this->getChildrenList()) {
                return $childElements;
            }
            //hack end
        } elseif ($this->mainMenuElements === null) {
            $this->mainMenuElements = [];
            $structureManager = $this->getService('structureManager');
            //TODO: remove hack required for catalogue filters
            $structureManager->getElementsChildren($this->id, 'container');
            if ($childElements = $this->getChildrenList()) {
                foreach ($childElements as &$childElement) {
                    if (!$childElement->hidden) {
                        $this->mainMenuElements[] = $childElement;
                    }
                }
            }
            //hack end
        }
        return $this->mainMenuElements;
    }

    public function getFirstPageElement()
    {
        if ($this->firstPageElement === null) {
            $structureManager = $this->getService('structureManager');
            if ($mainMenuElements = $structureManager->getElementsChildren($this->id, 'container')) {
                $this->firstPageElement = reset($mainMenuElements);
            } else {
                $this->firstPageElement = false;
            }
        }
        return $this->firstPageElement;
    }

    public function getCurrentMainMenu()
    {
        if ($this->currentMainMenu === null) {
            $this->currentMainMenu = false;
            if ($mainMenuElements = $this->getMainMenuElements(true)) {
                foreach ($mainMenuElements as &$element) {
                    if ($element->requested) {
                        $this->currentMainMenu = $element;
                        break;
                    }
                }
            }
            if (!$this->currentMainMenu) {
                $structureManager = $this->getService('structureManager');
                $controller = $this->getService('controller');
                if ($chain = $structureManager->getElementsChain($controller->requestedPath)) {
                    $this->currentMainMenu = last($chain);
                }
            }
        }
        return $this->currentMainMenu;
    }

    public function getTextContent()
    {
        if (is_null($this->textContent)) {
            $this->textContent = $this->title . " (" . $this->iso6393 . ")";
        }
        return $this->textContent;
    }

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['iso6392'] = 'text';
        $moduleStructure['iso6393'] = 'text';
        $moduleStructure['group'] = 'text';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image'] = 'image';
        $moduleStructure['backgroundImageOriginalName'] = 'fileName';
        $moduleStructure['backgroundImage'] = 'image';
        $moduleStructure['logoImageOriginalName'] = 'fileName';
        $moduleStructure['logoImage'] = 'image';
        $moduleStructure['hidden'] = 'checkbox';
        $moduleStructure['patternBackground'] = 'checkbox';
    }

    public function getElementFromMobileMenu($structureType)
    {
        return $this->getElementFromContextByType('mobileMenu', $structureType);
    }

    public function getElementsFromMobileMenu($structureType)
    {
        return $this->getElementsFromContextByType('mobileMenu', $structureType);
    }

    public function getMobileMenuElementsList()
    {
        return $this->getElementsFromContext('mobileMenu');
    }

    public function getMobileMenuElementsIndex()
    {
        return $this->getElementsTypesIndexFromContext('mobileMenu');
    }

    public function getElementFromMobileHeader($structureType, $number = 0)
    {
        $this->logError('Deprecated method used ' . __CLASS__ . ' ' . __METHOD__);
    }

    public function getHeaderElementsList()
    {
        return $this->getElementsFromContext('headerContent');
    }

    public function getHeaderElementsIndex()
    {
        return $this->getElementsTypesIndexFromContext('headerContent');
    }

    public function getElementFromHeader($structureType, $number = 0)
    {
        return $this->getElementFromContextByType('headerContent', $structureType, $number);
    }

    public function getElementsFromHeader($structureType)
    {
        return $this->getElementsFromContextByType('headerContent', $structureType);
    }

    public function getFooterElementsList()
    {
        return $this->getElementsFromContext('bottomMenu');
    }

    public function getFooterElementsIndex()
    {
        return $this->getElementsTypesIndexFromContext('bottomMenu');
    }

    public function getElementFromFooter($structureType, $number = 0)
    {
        return $this->getElementFromContextByType('bottomMenu', $structureType, $number);
    }

    public function getElementsFromFooter($structureType)
    {
        return $this->getElementsFromContextByType('bottomMenu', $structureType);
    }

    public function getSecondaryElements()
    {
        return array_merge($this->getHeaderElementsList(), $this->getLeftColumnElementsList());
    }

    public function getLeftColumnElementsList()
    {
        $result = [];
        if ($currentMainMenu = $this->getCurrentMainMenu()) {
            if ($currentMainMenu->columns == 'both' || $currentMainMenu->columns == 'left') {
                $result = $this->getElementsFromContext('leftColumn');
            }
        }
        return $result;
    }

    public function getElementFromLeftColumn($structureType, $number = 0)
    {
        foreach ($this->getLeftColumnElementsList() as $element) {
            if ($element->structureType == $structureType) {
                if ($number == 0) {
                    return $element;
                }
                $number--;
            }
        }
        return false;
    }

    public function getElementFromRightColumn($structureType, $number = 0)
    {
        foreach ($this->getRightColumnElementsList() as $element) {
            if ($element->structureType == $structureType) {
                if ($number == 0) {
                    return $element;
                }
                $number--;
            }
        }
        return false;
    }

    public function getRightColumnElementsList()
    {
        $result = [];
        if ($currentMainMenu = $this->getCurrentMainMenu()) {
            if ($currentMainMenu->columns == 'both' || $currentMainMenu->columns == 'right') {
                $result = $this->getElementsFromContext('rightColumn');
            }
        }
        return $result;
    }

    protected function loadElements($context)
    {
        if (isset($this->elementsList[$context])) {
            return;
        }
        $this->elementsList[$context] = [];
        $this->elementsIndex[$context] = [];
        $elements = &$this->elementsList[$context];
        $currentMainMenu = $this->getCurrentMainMenu();
        if ($currentMainMenu) {
            $elements = $this->getEnabledElementsByType($currentMainMenu->id, $context);
        }
        foreach ($elements as $element) {
            if (!isset($this->elementsIndex[$context][$element->structureType])) {
                $this->elementsIndex[$context][$element->structureType] = [];
            }
            $this->elementsIndex[$context][$element->structureType][] = $element;
        }
    }

    protected function getElementsFromContext($context)
    {
        $this->loadElements($context);
        return isset($this->elementsList[$context])
            ? $this->elementsList[$context] : [];
    }

    protected function getElementsTypesIndexFromContext($context)
    {
        $this->loadElements($context);
        return isset($this->elementsIndex[$context])
            ? $this->elementsIndex[$context] : [];
    }

    protected function getElementsFromContextByType($context, $type)
    {
        $this->loadElements($context);
        return isset($this->elementsIndex[$context][$type])
            ? $this->elementsIndex[$context][$type] : [];
    }

    protected function getElementFromContextByType($context, $type, $number = 0)
    {
        $elements = $this->getElementsFromContextByType($context, $type);
        return isset($elements[$number]) ? $elements[$number] : [];
    }

    protected function getEnabledElementsByType($menuId, $type)
    {
        $result = [];
        $linkType = 'displayinmenu';

        $linksManager = $this->getService('linksManager');
        $structureManager = $this->getService('structureManager');

        $enabledElementsIdList = $linksManager->getConnectedIdList($this->id, $type, 'parent');
        $languageEnabledElementsIdList = $linksManager->getConnectedIdList($this->id, $linkType, 'parent');
        $currentMenuEnabledElementsIdList = $linksManager->getConnectedIdList($menuId, $linkType, 'parent');

        // todo: this now only supports the direct children of current menu. We should somehow allow any level to be used
        if ($currentMainPage = $this->getCurrentMainMenu()) {
            if (!$currentMainPage->final) {
                $subPages = $currentMainPage->getChildrenList();
                foreach ((array)$subPages as $subPage) {
                    if ($subPage->requested) {
                        $currentMenuEnabledElementsIdList = array_merge($currentMenuEnabledElementsIdList, (array)($linksManager->getConnectedIdList($subPage->id, $linkType, 'parent')));
                        break;
                    }
                }
            }
        }

        if ($resultIdList = array_intersect(array_merge($languageEnabledElementsIdList, $currentMenuEnabledElementsIdList), $enabledElementsIdList)) {
            $result = $structureManager->getElementsByIdList($resultIdList, $this->id, $type);
        }
        return $result;
    }

    public function getChildrenList($roles = null, $linkType = 'structure', $allowedTypes = null, $useBlackList = false)
    {
        $controller = controller::getInstance();
        $applicationName = $controller->getApplicationName();
        // TODO: perhaps check for admin app instead
        if ($applicationName == 'public') {
            return parent::getChildrenList($roles, $linkType, $allowedTypes, $useBlackList);
        } else {
            //todo: review this code and remove $urlString
            $structureManager = $this->getService('structureManager');
            $linksManager = $this->getService('linksManager');
            $contentType = $controller->getParameter('view') ? $controller->getParameter('view') : 'structure';

            $idList = $linksManager->getConnectedIdList($this->id, $contentType, 'parent');
            $childrenList = $structureManager->getElementsByIdList($idList, $this->id, $contentType);
            if ($contentType != 'structure') {
                $urlString = 'view:' . $contentType . '/';
                foreach ($childrenList as &$element) {
                    if (!stripos($element->URL, $urlString)) {
                        $element->URL .= $urlString;
                    }
                }
            }

            return $childrenList;
        }
    }

    public function getNewElementUrl()
    {
        $controller = controller::getInstance();
        $url = $this->URL;
        if ($controller->getApplicationName() != 'adminAjax') {
            if ($controller->getParameter('view') && $this->final) {
                $contentType = $controller->getParameter('view');
                $url .= 'view:' . $contentType . '/';
            }
        }
        return $url;
    }

    /**
     * @return array
     * @deprecated
     */
    public function getMenuElements()
    {
        return $this->getMainMenuElements();
    }

    public function getFormActionURL($type = null)
    {
        $controller = controller::getInstance();
        if ($contentType = $controller->getParameter('view')) {
            return $this->URL . 'view:' . $contentType . '/';
        }

        return $this->URL;
    }

    /**
     * Get allowed children structure elements type according to settings, current user's privileges and selected type
     *
     * @param string $childCreationAction - name of action for adding the child element. Default controlled action is 'showForm'
     * @return string[]
     */
    public function getAllowedChildStructureTypes($childCreationAction = 'showForm')
    {
        if (is_null($this->allowedChildStructureTypes)) {
            $contentType = 'structure';
            $controller = controller::getInstance();
            if ($controller->getApplicationName() != 'adminAjax') {
                if ($controller->getParameter('view')) {
                    $contentType = $controller->getParameter('view');
                }
            }
            if ($contentType == 'headerContent') {
                $allowedTypes = $this->allowedTypesHeader;
            } elseif ($contentType == 'bottomMenu') {
                $allowedTypes = $this->allowedTypesFooter;
            } elseif ($contentType == 'mobileMenu') {
                $allowedTypes = $this->allowedTypesMobile;
            } elseif ($contentType != 'structure') {
                $allowedTypes = $this->allowedTypesColumns;
            } else {
                $allowedTypes = $this->allowedTypes;
            }

            $this->allowedChildStructureTypes = [];
            $privilegesManager = $this->getService('privilegesManager');
            $privileges = $privilegesManager->getElementPrivileges($this->id);

            foreach ($allowedTypes as &$type) {
                if (isset($privileges[$type]) && isset($privileges[$type][$childCreationAction]) && $privileges[$type][$childCreationAction] === true) {
                    $this->allowedChildStructureTypes[] = $type;
                }
            }
        }
        return $this->allowedChildStructureTypes;
    }

    public function getMostSuitableHeaderGallery()
    {
        $controller = controller::getInstance();
        $structureManager = $this->getService('structureManager');
        $currentElement = $structureManager->getCurrentElement($controller->requestedPath);
        $galleries = $this->getElementsFromHeader('gallery');
        $betterGallery = false;

        foreach ($galleries as $gallery) {
            foreach ($gallery->getDisplayMenusInfo() as $info) {
                if ($info['id'] == $currentElement->id && $info['linkExists']) {
                    $betterGallery = $gallery;
                }
            }
        }

        if ($betterGallery) {
            return $betterGallery;
        } else {
            return $this->getElementFromHeader('gallery');
        }
    }

    public function getLogoImageUrl($preset = 'logo')
    {
        $result = '';
        if ($this->logoImage) {
            $controller = controller::getInstance();
            $result = $controller->baseURL . 'image/type:' . $preset . '/id:' . $this->logoImage
                . '/filename:' . $this->logoImageOriginalName;
        }

        if (!$result) {
            $designThemesManager = $this->getService('designThemesManager');
            $result = $designThemesManager->getCurrentTheme()->getImageUrl('logo.png', false, false);
        }

        return $result;
    }
}