<?php
declare(strict_types=1);


final class LanguageLinksService
{
    public function __construct(
        private controller       $controller,
        private ConfigManager    $configManager,
        private LanguagesManager $languagesManager,
        private structureManager $structureManager,
    )
    {

    }

    public function getLanguageLinks(
        structureElement $element,
    ): array
    {
        $links = [];

        $currentLanguageId = $this->languagesManager->getCurrentLanguageId();
        foreach ($this->languagesManager->getLanguagesList($this->configManager->get('main.rootMarkerPublic')) as $language) {
            if ($language->id === $currentLanguageId) {
                $links[$language->iso6391] = $element->getUrl();
                continue;
            }
            if (!$element->hasActualStructureInfo()) {
                continue;
            }
            $path = $this->structureManager->findPath($element->id, (int)$language->id);
            if ($path === null) {
                continue;
            }
            $urlName = $this->controller->getApplication()->getUrlName();
            $languageUrl = $urlName !== '' ? $urlName . '/' : '';
            $links[$language->iso6391] = $this->controller->baseURL . $languageUrl . implode('/', $path) . '/';
        }

        return $links;
    }
}