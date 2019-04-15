<?php

class breadcrumbsManager implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $breadcrumbs;
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getBreadcrumbs($useMinAmount = true, $useAllowedElementTypes = true)
    {
        $this->breadcrumbs = [];
        /**
         * @var structureManager $structureManager
         */
        $structureManager = $this->getService('structureManager');
        if ($useAllowedElementTypes && ($currentElement = $structureManager->getCurrentElement())) {
            if (!in_array($currentElement->structureType, $this->getAllowedElementTypes())) {
                return [];
            }
        }
        $controller = controller::getInstance();
        $minLevel = $this->getMinLevel();
        $minAmount = $this->getMinAmount();
        foreach ($structureManager->getElementsChain($controller->requestedPath) as $crumb) {
            if ($crumb->level >= $minLevel) {
                $this->breadcrumbs[] = [
                    'URL' => $crumb->URL,
                    'title' => $crumb->getTitle(),
                ];
            }
        }
        if ($useMinAmount && (count($this->breadcrumbs) < $minAmount)) {
            $this->breadcrumbs = [];
        }

        return $this->breadcrumbs;
    }

    public function appendBreadcrumb($link, $title)
    {
        $this->breadcrumbs[] = [
            'URL' => $link,
            'title' => $title,
        ];
    }

    protected function getMinLevel()
    {
        if (!empty($this->config)) {
            return $this->config->get('minLevel');
        }
        return 0;
    }

    protected function getMinAmount()
    {
        if (!empty($this->config)) {
            return $this->config->get('minAmount');
        }
        return 0;
    }

    protected function getAllowedElementTypes()
    {
        if (!empty($this->config)) {
            return $this->config->get('allowedElementTypes');
        }
        return [];
    }

    public function getLdJson()
    {
        $result = [
            "@context" => "http://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [],
        ];
        foreach ($this->getBreadcrumbs(false, false) as $key => $item) {
            $result["itemListElement"][] = [
                "@type" => "ListItem",
                "position" => $key + 1,
                "name" => $item['title'],
                "item" => $item['URL'],
            ];
        }
        return json_encode($result);
    }
}

