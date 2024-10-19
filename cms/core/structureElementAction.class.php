<?php

abstract class structureElementAction extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    protected $loggable = false;
    public $actionName = false;
    protected $validated = true;
    public $expectedFields = [];
    public $validators = [];
    /**
     * @var structureElement $structureElement
     */
    public $structureElement;
    protected $elementFormData;

    public function __construct()
    {
    }

    public function getPrivilegeName()
    {
        return $this->actionName;
    }

    public function processFormData()
    {
        if (method_exists($this, 'setExpectedFields')) {
            $this->setExpectedFields($this->expectedFields);
        }
        if (count($this->expectedFields) > 0) {
            $controller = controller::getInstance();
            if ($elementFormData = $controller->getElementFormData($this->structureElement->id)) {
                $this->elementFormData = $elementFormData;
                if (method_exists($this, 'setValidators')) {
                    $this->setValidators($this->validators);
                }
                $this->validated = $this->structureElement->importExternalData($elementFormData, $this->expectedFields, $this->validators);
            }
        }
    }

    public function startAction()
    {
        if (method_exists($this, 'getExtraModuleFields')) {
            if ($moduleFields = $this->getExtraModuleFields()) {
                $this->structureElement->addModuleFields($moduleFields);
            }
        }
        $this->processFormData();

        $controller = controller::getInstance();
        $structureManager = $this->getService('structureManager');

        if ($this->loggable) {
            $this->logAction();
        }

        $this->execute($structureManager, $controller, $this->structureElement);
    }

    protected function getTranslations($marker = 'public_translations', $languageId = false)
    {
        $translations = false;
        if (!$languageId) {
            $languagesManager = $this->getService('LanguagesManager');
            $languageId = $languagesManager->getCurrentLanguageId();
        }
        if ($languageId) {
            $translationManager = $this->getService('translationsManager');
            $translations = $translationManager->getTranslationsList($marker, $languageId);
        }
        return $translations;
    }

    /**
     * @abstract
     * @param structureManager $structureManager
     * @param controller $controller
     * @param structureElement $structureElement
     * @return mixed
     */
    abstract public function execute(&$structureManager, &$controller, &$structureElement);

    protected function logAction()
    {
        $collection = persistableCollection::getInstance('actions_log');
        $record = $collection->getEmptyObject();
        $user = $this->getService('user');

        $record->elementId = $this->structureElement->id;
        $record->elementType = $this->structureElement->structureType;
        $record->elementName = $this->structureElement->structureName;
        $record->action = $this->actionName;
        $record->userId = $user->id;
        $record->userIP = $user->IP;
        $record->userName = $user->userName;
        $record->date = time();
        $record->persist();
    }

    protected function getErrorLogLocation(): string
    {
        return $this->structureElement->title . " " . $this->structureElement->structureType . " " . $this->structureElement->id;
    }
}