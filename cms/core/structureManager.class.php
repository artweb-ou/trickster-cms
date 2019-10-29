<?php

use Illuminate\Database\Connection;

class structureManager implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    protected $elementsList = [];
    protected $elementsParents = [];

    protected $elementsDataCollection;
    protected $loadedFiles = [];

    protected $cachedMarkers = [];

    public $rootElementId;
    protected $rootElementMarker;
    protected $newElement;
    protected $currentElement;
    public $newElementParameters;
    protected $newElementLinkType = 'structure';
    /**
     * @var Cache
     */
    protected $cache;
    protected $cacheLifeTime = 1800;

    /**
     * @var privilegesManager
     */
    protected $privilegesManager;
    /**
     * @var linksManager
     */
    protected $linksManager;
    /**
     * @var LanguagesManager
     */
    protected $languagesManager;
    protected $defaultRoles = [];

    protected $requestedPath = [];
    protected $requestedPathString = '';
    public $allowedRoles = [];
    public $customActions = [];
    public $defaultActions = [];
    protected $pathSearchAllowedLinks;
    public $rootURL = "";
    protected $privilegeChecking = true;
    protected $deniedCopyLinkTypes = [];
    protected $elementPathRestrictionId;
    protected $shortestChains = [];

    /**
     * @param LanguagesManager $languagesManager
     */
    public function setLanguagesManager($languagesManager)
    {
        $this->languagesManager = $languagesManager;
    }

    public function setLinksManager(linksManager $linksManager)
    {
        $this->linksManager = $linksManager;
    }

    public function setPrivilegesManager(privilegesManager $privilegesManager)
    {
        $this->privilegesManager = $privilegesManager;
    }

    /**
     * @param Cache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    public function __destruct()
    {
        //        $this->elementsLoadReport();
    }

    public function __construct()
    {
        $this->defaultRoles = [
            'content',
            'container',
            'hybrid',
        ];
        $this->elementsDataCollection = persistableCollection::getInstance('structure_elements');
    }

    /**
     * Returns the 1-dimensional array of complete structureElements tree.
     *
     * @param int $elementId
     * @param int $roles
     * @param string $linkType
     * @param bool $restrictLinkTypes
     * @param structureElement[] $flatTree
     * @param array $usedIds - prevents cycling
     * @return structureElement[]
     */
    public function getElementsFlatTree(
        $elementId,
        $roles = null,
        $linkType = 'structure',
        $restrictLinkTypes = false,
        &$flatTree = [],
        &$usedIds = []
    ) {
        $treeLevel = $this->getElementsChildren($elementId, $roles, $linkType, null, $restrictLinkTypes);
        foreach ($treeLevel as &$element) {
            if (!in_array($element->id, $usedIds)) {
                $usedIds[] = $element->id;
                $flatTree[] = $element;
                $this->getElementsFlatTree($element->id, $roles, $linkType, $restrictLinkTypes, $flatTree, $usedIds);
            }
        }
        return $flatTree;
    }

    /**
     * Returns one-dimensional array of all elements mentioned in structurePath
     * @param string[] $structurePath
     * @param int $parentElementId
     * @param structureElement[] $elementsChain
     * @return structureElement[]
     */
    public function getElementsChain($structurePath = [], $parentElementId = null, &$elementsChain = [])
    {
        if ($structurePath) {
            if (is_null($parentElementId)) {
                $parentElementId = $this->getRootElementId();
            }
            //take the first element name from the path array
            $currentStructureName = array_shift($structurePath);

            //search for the element by its structureName within the current parent element's children
            if ($element = $this->getElementByStructureName($currentStructureName, $parentElementId)) {
                $elementsChain[] = $element;
                //make recursive call to getElementsChain() using the found child element as parent
                $this->getElementsChain($structurePath, $element->id, $elementsChain);
            }
        }
        return $elementsChain;
    }

    /**
     * Searches and returns the element defined by it's path
     *
     * @param string[] $structurePath
     * @param int $parentElementId
     * @return structureElement|bool
     */
    public function getElementByPath($structurePath = null, $parentElementId = null)
    {
        if ($structurePath) {
            if ($parentElementId === null) {
                $parentElementId = $this->getRootElementId();
            }

            //take the first element name from the path array
            $currentStructureName = array_shift($structurePath);

            //search for the element by its structureName within the current parent element's children
            if ($element = $this->getElementByStructureName($currentStructureName, $parentElementId)) {
                if ($structurePath) {
                    if ($childElement = $this->getElementByPath($structurePath, $element->id)) {
                        return $childElement;
                    } else {
                        return false;
                    }
                } else {
                    return $this->elementsList[$element->id];
                }
            } else {
                return false;
            }
        } else {
            return $this->getRootElement();
        }
    }

    /**
     * Searches for all elements of requested type restricted with the defined parent element if required
     *
     * @param string $structureType
     * @param int $parentElementId
     * @param string[] $orderFields
     * @param int|int[] $limit
     * @return structureElement[]
     */
    public function getElementsByType($structureType, $parentElementId = null, $orderFields = [], $limit = [])
    {
        if (is_null($parentElementId)) {
            $parentElementId = $this->getRootElementId();
        }
        $result = [];

        if ($foundObjects = $this->elementsDataCollection->load(
            ['structureType' => $structureType],
            $orderFields,
            false,
            $limit
        )
        ) {
            foreach ($foundObjects as &$dataObject) {
                if ($this->checkElementInParent($dataObject->id, $parentElementId)) {
                    if ($newElement = $this->getElementById($dataObject->id, $parentElementId)) {
                        $result[] = $newElement;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * This method doesn't load new elements, but it can search for the known type within the already loaded elements list
     *
     * @param string $type
     * @return structureElement[]
     */
    public function getLoadedElementsByType($type)
    {
        $elements = [];
        foreach ($this->elementsList as &$element) {
            if ($element && $element->structureType == $type) {
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * Returns the list of element's parent elements according to provided link type
     *
     * @param int $elementId
     * @param string $linkType
     * @param bool $restrictLinkTypes
     * @return structureElement[]
     */
    public function getElementsParents($elementId, $linkType = null, $restrictLinkTypes = true)
    {
        if (!$linkType) {
            $linkType = '';
        }
        if (!isset($this->elementsParents[$elementId][$linkType])) {
            $this->elementsParents[$elementId][$linkType] = [];
            if ($restrictLinkTypes && !$linkType) {
                $elementsLinks = $this->linksManager->getElementsLinks($elementId, $this->getPathSearchAllowedLinks(), 'child');
            } else {
                $elementsLinks = $this->linksManager->getElementsLinks($elementId, $linkType, 'child');
            }

            foreach ($elementsLinks as $link) {
                if ($element = $this->getElementById($link->parentStructureId)) {
                    $this->elementsParents[$elementId][$linkType][] = $element;
                }
            }
        }
        return $this->elementsParents[$elementId][$linkType];
    }

    /**
     * Returns the first parent of element specified by id
     *
     * @param int $elementId
     * @param null $linkType
     * @return bool|structureElement
     */
    public function getElementsFirstParent($elementId, $linkType = null)
    {
        $result = false;
        if ($parentsList = $this->getElementsParents($elementId, $linkType)) {
            $result = reset($parentsList);
        }
        return $result;
    }

    /**
     * Returns the requested parent of element specified by id
     *
     * @param int $elementId
     * @param null $linkType
     * @return bool|structureElement
     */
    public function getElementsRequestedParent($elementId, $linkType = null)
    {
        if ($parentsList = $this->getElementsParents($elementId, $linkType)) {
            foreach ($parentsList as $parentElement) {
                if ($parentElement->requested) {
                    return $parentElement;
                }
            }
        }
        return false;
    }

    /**
     * Deprecated, use setRequestedPath and getRootElement instead
     *
     * @param string[] $controllerRequestedPath
     *
     * @return bool|structureElement
     * @deprecated
     */
    public function buildRequestedPath($controllerRequestedPath = [])
    {
        $this->logError('deprecated method buildRequestedPath used, use setRequestedPath instead');
        $this->setRequestedPath($controllerRequestedPath);

        $elementId = $this->getRootElementId();

        //check if element is already loaded from the storage
        if (isset($this->elementsList[$elementId]) && is_object($this->elementsList[$elementId])) {
            return $this->elementsList[$elementId];
        } else {
            //load element from the storage
            if ($element = $this->loadRootElement($elementId)) {
                return $element;
            }
        }
        return false;
    }

    public function setRequestedPath($requestedPath)
    {
        $this->requestedPath = $requestedPath;
        if ($requestedPath) {
            $this->requestedPathString = implode('/', $requestedPath) . '/';
        } else {
            $this->requestedPathString = '';
        }
    }

    /**
     * Current element is an element which should be displayed for the current requested URL
     *
     * @param string[] $currentElementPath
     * @return bool|structureElement
     */
    public function getCurrentElement($currentElementPath = null)
    {
        if ($this->currentElement === null) {
            if ($currentElementPath === null) {
                $currentElementPath = $this->requestedPath;
            }
            if (isset($this->newElementParameters[$this->requestedPathString])) {
                $this->requestedPathString .= "type:" . $this->newElementParameters[$this->requestedPathString]["type"] . "/";
            }
            //check if there was a new element under the current path and create it
            if ($currentElementPath) {
                $currentElementPathString = implode('/', $currentElementPath) . '/';
            } else {
                $currentElementPathString = '';
            }
            if (isset($this->newElementParameters[$currentElementPathString])) {
                if ($parentElement = $this->getElementByPath($currentElementPath)) {
                    //this check guarantees that new element would be created only once
                    if (isset($this->newElementParameters[$currentElementPathString])) {
                        $newElementAction = $this->newElementParameters[$currentElementPathString]['action'];
                        $newElementType = $this->newElementParameters[$currentElementPathString]['type'];

                        unset($this->newElementParameters[$currentElementPathString]);
                        $this->currentElement = $this->createElement(
                            $newElementType,
                            $newElementAction,
                            $parentElement->id,
                            true
                        );
                    }
                }
            }

            //get current element according to requested path
            if (!$this->currentElement) {
                $this->currentElement = $this->getElementByPath($currentElementPath);
            }
        }

        return $this->currentElement;
    }

    public function setCurrentElement($currentElement)
    {
        $this->currentElement = $currentElement;
        $currentElement->final = true;
        $currentElement->requested = true;
    }

    /**
     * Loads root element from storage
     *
     * @param int $elementId
     * @return bool|structureElement
     */
    protected function loadRootElement($elementId = null)
    {
        if ($elementId === null) {
            $elementId = $this->getRootElementId();
        }
        $element = false;

        //load element from the storage
        if ($elementsList = $this->loadElementsToParent([$elementId])) {
            $element = array_shift($elementsList);
        }

        return $element;
    }

    /**
     * Preload and return root element
     *
     * @return bool|structureElement
     */
    public function getRootElement()
    {
        if (isset($this->elementsList[$this->getRootElementId()])) {
            $element = $this->elementsList[$this->getRootElementId()];
        } else {
            $element = $this->loadRootElement();
        }

        return $element;
    }

    /**
     * Searches for the element with specified structure name within specified parent element's children. Makes a search in storage and loads the element if required
     *
     * @param string $childElementName
     * @param int $parentElementId
     * @return bool|structureElement
     */
    public function getElementByStructureName($childElementName, $parentElementId)
    {
        $result = false;
        if ($childElementName) {
            if (!isset($this->elementsList[$parentElementId])) {
                $this->elementsList[$parentElementId] = $this->getElementById($parentElementId);
            }
            if (isset($this->elementsList[$parentElementId])) {
                $cacheKey = $parentElementId . ':e:' . 'name' . $childElementName;
                if ($id = $this->cache->get($cacheKey)) {
                    return $this->getElementById($id);
                }

                $parentElement = $this->elementsList[$parentElementId];
                foreach ($parentElement->childrenList as &$element) {
                    if ($element->structureName == $childElementName) {
                        $result = $this->elementsList[$element->id];
                        break;
                    }
                }
                if (!$result) {
                    //this shouldn't be switched to 'getConnectedIds' because of speed issues. benchmark first
                    $connectedLinks = $this->linksManager->getElementsLinks($parentElementId, $this->getPathSearchAllowedLinks(), 'parent');
                    $connectedIds = [];
                    foreach ($connectedLinks as $link) {
                        $connectedIds[] = $link->childStructureId;
                    }
                    /**
                     * @var \Illuminate\Database\Query\Builder $query
                     */
                    $query = $this->getService('db')->table('structure_elements');
                    $query
                        ->select('id')
                        ->where('structureName', '=', $childElementName)
                        ->whereIn('id', $connectedIds)
                        ->limit(1);

                    if ($record = $query->first()) {
                        $id = $record['id'];
                        $this->loadElementsToParent([$id], $parentElementId);
                        if (isset($this->elementsList[$id])) {
                            $result = $this->elementsList[$id];
                        }
                    }
                }
                if (!$result) {
                    $this->getElementsChildren($parentElement->id);
                    foreach ($parentElement->childrenList as &$element) {
                        if ($element->structureName == $childElementName) {
                            $result = $this->elementsList[$element->id];
                        }
                    }
                }
                if ($result) {
                    $this->cache->set($cacheKey, $result->id, 600);
                }

            }
        }

        return $result;
    }

    /**
     * Sets the new parent element for specified element
     *
     * @param int $sourceParentId
     * @param int $targetId
     * @param int $elementId
     * @param string $linkType
     */
    public function moveElement($sourceParentId, $targetId, $elementId, $linkType = null)
    {
        $elementLinks = $this->linksManager->getElementsLinks($elementId, '', 'child');
        foreach ($elementLinks as &$link) {
            if ($link->parentStructureId == $sourceParentId && ($linkType === null || $linkType == $link->type)) {
                $link->parentStructureId = $targetId;
                $link->persist();
            }
        }
        $parentLinks = $this->linksManager->getElementsLinks($elementId, 'parent');
        foreach ($parentLinks as &$link) {
            if ($link->parentStructureId == $sourceParentId && ($linkType === null || $linkType == $link->type)) {
                $link->parentStructureId = $targetId;
                $link->persist();
            }
        }
    }

    /**
     * Copies a list of elements into a new parent element
     *
     * @param int[] $idList - list of id numbers of elements to copy
     * @param int $targetId - new parent element id
     * @param string[] $linkTypes - link types of copied connected elements
     * @param null $parentLinkType - all copied top-elements will use this to link with new parents
     * @return array $copiesInformation
     */
    public function copyElements($idList, $targetId, $linkTypes = null)
    {
        //by default copy only elements via "structure" links
        if (is_null($linkTypes)) {
            $linkTypes = ['structure'];
        }

        //copy all the elements including their structure children tree
        $copiesInformation = [];
        foreach ($idList as $sourceId) {
            $parentLinkType = false;
            if ($elementLinks = $this->linksManager->getElementsLinks($sourceId, null, 'child')) {
                foreach ($elementLinks as $elementLink) {
                    if (in_array($elementLink->type, $linkTypes)) {
                        $parentLinkType = $elementLink->type;
                        break;
                    }
                }

                if ($parentLinkType) {
                    $this->copyElement($sourceId, $targetId, $parentLinkType, $linkTypes, $copiesInformation);
                }
            }
        }

        //replicate all copied elements links using the information from old elements to maintain same connections
        foreach ($copiesInformation as $sourceId => &$newId) {
            if ($elementLinks = $this->linksManager->getElementsLinks($sourceId, null)) {
                foreach ($elementLinks as &$link) {
                    if (!in_array($link->type, $linkTypes) && !in_array($link->type, $this->deniedCopyLinkTypes)) {
                        if ($link->parentStructureId == $sourceId) {
                            $connectedId = $link->childStructureId;
                            if (isset($copiesInformation[$connectedId])) {
                                $connectedId = $copiesInformation[$connectedId];
                            }
                            $this->linksManager->linkElements($newId, $connectedId, $link->type);
                        } elseif ($link->childStructureId == $sourceId) {
                            $connectedId = $link->parentStructureId;
                            if (isset($copiesInformation[$connectedId])) {
                                $connectedId = $copiesInformation[$connectedId];
                            }
                            $this->linksManager->linkElements($connectedId, $newId, $link->type);
                        }
                    }
                }
            }
        }
        return $copiesInformation;
    }

    /**
     * makes a copy of one element and returns it
     *
     * @param int $sourceId - source element id
     * @param int $targetId - id number of new parent element
     * @param $currentLinkType - link type of newly created element to connect with previous iteration parent
     * @param string[] $linkTypes - link types of copied connected elements
     * @param int[] $copiesInformation - index of old element relations to newly created element ID relations, filled automatically
     * @return bool|structureElement
     */
    protected function copyElement($sourceId, $targetId, $currentLinkType, $linkTypes, &$copiesInformation = [])
    {
        $newElement = false;
        if ($sourceElement = $this->getElementById($sourceId)) {
            $sourceStructureData = $sourceElement->getStructureData();
            //copy of product shouldn't contain creation/modification dates of original
            unset($sourceStructureData['dateCreated']);
            unset($sourceStructureData['dateModified']);
            $sourceModuleData = $sourceElement->getModuleData();

            $structureDataObject = $this->elementsDataCollection->getEmptyObject();
            $structureDataObject->setData($sourceStructureData);
            $structureDataObject->dateCreated = time();
            $structureDataObject->persist();

            $moduleResourceName = $sourceElement->getDataResourceName();

            //todo: are $moduleDataObjects required here? possibly yes, investigate
            $moduleDataObjects = [];
            foreach ($sourceModuleData as $languageId => &$languageData) {
                $collection = persistableCollection::getInstance($moduleResourceName);
                $moduleDataObject = $collection->getEmptyObject();
                $moduleDataObject->setData($languageData);
                $moduleDataObject->id = $structureDataObject->id;
                $moduleDataObject->languageId = $languageId;
                $moduleDataObject->persist();

                $moduleDataObjects[$languageId] = $moduleDataObject;
            }
            $this->linksManager->createLinkObject($targetId, $structureDataObject->id, $currentLinkType);

            if ($newElement = $this->manufactureElement($structureDataObject, $targetId)) {
                $newElement->prepareActualData();
                $newElement->structureName = $newElement->getTitle();
                $newElement->copyExtraData($sourceElement->id);
                $newElement->persistElementData();

                $copiesInformation[$sourceId] = $newElement->id;
                foreach ($linkTypes as &$linkType) {
                    if ($childrenList = $this->getElementsChildren($sourceElement->id, null, $linkType)) {
                        foreach ($childrenList as $childElement) {
                            $this->copyElement($childElement->id, $newElement->id, $linkType, $linkTypes, $copiesInformation);
                        }
                    }
                }

                $this->performAction($newElement);
            }
        }
        return $newElement;
    }

    public function moveElements($idList, $targetId, $linkTypes = null)
    {
        if (is_null($linkTypes)) {
            $linkTypes = ['structure'];
        }
        foreach ($idList as &$elementId) {
            foreach ($linkTypes as &$linkType) {
                if ($parentIdList = $this->linksManager->getConnectedIdList($elementId, $linkType, 'child')) {
                    foreach ($parentIdList as &$parentId) {
                        $this->linksManager->unLinkElements($parentId, $elementId, $linkType);
                    }
                    $this->linksManager->linkElements($targetId, $elementId, $linkType);
                }
            }
        }
        return true;
    }

    /**
     * Changes default link type for all created new elements.
     *
     * @param string $linkType
     */
    public function setNewElementLinkType($linkType = null)
    {
        if (!$linkType) {
            $linkType = 'structure';
        }
        $this->newElementLinkType = $linkType;
    }

    /**
     * Error logging
     *
     * @param string $text
     */
    public function logError($text)
    {
        $errorLog = errorLog::getInstance();
        $errorLog->logMessage('structureManager', $text);
    }

    /**
     * Creates an empty structure element with empty data
     *
     * @param string $type
     * @param string $action
     * @param int $parentElementId
     * @param bool $setCurrent
     * @return bool|structureElement
     */
    public function createElement($type, $action, $parentElementId = null, $setCurrent = false, $linkType = null)
    {
        if ($parentElementId === null) {
            $parentElementId = $this->getRootElementId();
        }
        $id = '';
        if ($parentElementId === 0) {
            $id = 'type:' . $type . '/action:' . $action;
        } elseif ($parentElement = $this->getElementById($parentElementId)) {
            $id = $parentElement->structurePath . 'type:' . $type . '/action:' . $action;
        }

        if ($id && !isset($this->elementsList[$id])) {
            $this->customActions[$id] = $action;

            $dataObject = $this->elementsDataCollection->getEmptyObject();
            $dataObject->id = $id;
            $dataObject->structureType = $type;
            $dataObject->structureName = '';

            //create temporary link object which will be automatically saved afterwards
            if ($parentElementId != 0) {
                if ($linkType !== null) {
                    $newElementLinkType = $linkType;
                } else {
                    $newElementLinkType = $this->newElementLinkType;
                }

                $this->linksManager->createLinkObject($parentElementId, $id, $newElementLinkType);
            }

            if ($newElement = $this->manufactureElement($dataObject, $parentElementId)) {
                $newElement->createEmptyModuleObjects();
                if ($setCurrent) {
                    $this->setCurrentElement($newElement);
                }
                $this->performAction($newElement);
            }
            return $newElement;
        }

        return false;
    }

    /**
     * @param $allowedRoles
     * @return array
     */
    protected function getRequestedRoles($allowedRoles)
    {
        if (is_null($allowedRoles)) {
            $requestedRoles = $this->defaultRoles;
        } else {
            if ($allowedRoles == 'content' || $allowedRoles == 'container') {
                $requestedRoles = [
                    $allowedRoles,
                    'hybrid',
                ];
            } elseif (!is_array($allowedRoles)) {
                $requestedRoles = [$allowedRoles];
            } else {
                $requestedRoles = $allowedRoles;
            }
        }
        return $requestedRoles;
    }

    /**
     * @param int $parentElementId
     * @param string[]|null $allowedRoles
     * @param string|string[] $linkTypes
     * @param string|string[]|null $allowedTypes
     * @param bool $restrictLinkTypes
     * @return structureElement[]
     */
    public function getElementsChildren(
        $parentElementId,
        $allowedRoles = null,
        $linkTypes = 'structure',
        $allowedTypes = null,
        $restrictLinkTypes = false
    ) {
        $returnList = [];
        if ($parentElement = $this->getElementById($parentElementId)) {
            $requestedRoles = $this->getRequestedRoles($allowedRoles);

            $rolesToLoad = $requestedRoles;
            //check if all required types of children elements are already loaded for this structure element
            foreach ($rolesToLoad as $key => &$role) {
                if ($parentElement->getChildrenLoadedStatus($linkTypes, $role)) {
                    unset($rolesToLoad[$key]);
                }
            }
            //get all structure links for this element
            if ($restrictLinkTypes) {
                if (!$linkTypes) {
                    $linkTypes = $this->getPathSearchAllowedLinks();
                }
                $elementsLinks = $this->linksManager->getElementsLinks($parentElementId, $linkTypes, 'parent');
            } else {
                $elementsLinks = $this->linksManager->getElementsLinks($parentElementId, $linkTypes, 'parent');
            }


            //make an array of children elements' structure ids
            $idListToLoad = [];
            $idListToReturn = [];
            foreach ($elementsLinks as &$elementsLink) {
                $childId = $elementsLink->childStructureId;
                $idListToReturn[] = $childId;
                if (!isset($this->elementsList[$childId]) || !is_object($this->elementsList[$childId])) {
                    $idListToLoad[] = $childId;
                }
            }

            if ($idListToLoad) {
                if ($this->privilegeChecking) {
                    //calculate required privileges for a postcheck
                    $this->privilegesManager->getAllowedElements($parentElementId, $idListToLoad);
                }

                if ($allowedTypes !== null) {
                    $allowedElements = $allowedTypes;
                } else {
                    $allowedElements = [];
                }

                foreach ($rolesToLoad as &$role) {
                    $parentElement->setChildrenLoadedStatus($linkTypes, $role, true);
                }

                //load the children elements from the storage and return them
                $this->loadElementsToParent($idListToLoad, $parentElementId, $allowedElements, $rolesToLoad);
            }

            foreach ($idListToReturn as &$childElementId) {
                if (isset($this->elementsList[$childElementId]) && ($childElement = $this->elementsList[$childElementId])) {
                    if (in_array($childElement->structureRole, $requestedRoles)) {
                        if (method_exists($childElement, 'getReplacementElements') &&
                            (is_array($replacementElements = $childElement->getReplacementElements($allowedRoles)))
                        ) {
                            //required for product catalogue-like elements
                            $returnList = array_merge($returnList, $replacementElements);
                        } else {
                            $returnList[] = $childElement;
                        }
                    }
                }
            }
        }
        return $returnList;
    }

    /**
     * @param structureElement $element
     */
    public function performAction($element)
    {
        if (!$this->privilegeChecking || $this->privilegesManager->checkPrivilegesForAction(
                $element->id,
                $element->actionName,
                $element->structureType
            )
        ) {
            $element->executeAction();
        } else {
            $this->logError('Insufficient privileges: element ID:' . $element->id . ' action:' . $element->actionName);
        }
    }

    /**
     * @param array $idList
     * @param int $parentElementId
     * @param array $allowedElements
     * @param array $allowedRoles
     * @return array|bool
     */
    protected function loadElementsToParent($idList = [], $parentElementId = 0, $allowedElements = [], $allowedRoles = [])
    {
        if (!$parentElementId) {
            $parentElementId = $this->getRootElementId();
        }
        $loadedElements = [];
        foreach ($idList as $key => $id) {
            if (isset($this->elementsList[$id]) && ($element = $this->elementsList[$id])) {
                $loadedElements[$key] = $element;
                unset($idList[$key]);
            } elseif ($element = $this->loadFromCache($id, $parentElementId)) {
                $loadedElements[$key] = $element;
                unset($idList[$key]);
            }
        }
        $searchFields = [];
        if ($idList) {
            $searchFields['id'] = $idList;
        } else {
            return $loadedElements;
        }

        if ($allowedElements) {
            $searchFields['structureType'] = $allowedElements;
        }
        //We have only 3 roles: content, container and hybrid.
        //If there are all roles required, then we don't need to restrict this column,
        //so we can save some sql resources by not sending this info
        if ($allowedRoles && count($allowedRoles) < 3) {
            $searchFields['structureRole'] = $allowedRoles;
        }

        //load elements from storage
        $loadedModuleTables = [];
        if ($dataObjects = $this->elementsDataCollection->load($searchFields, ['id' => $idList])) {
            foreach ($dataObjects as &$dataObject) {
                $elementId = $dataObject->id;
                if ($loadedElement = $this->manufactureElement($dataObject, $parentElementId)) {
                    $this->setElementCacheKey($elementId, 'e', $this->elementsList[$elementId], $this->cacheLifeTime);

                    $loadedElements[$elementId] = $this->elementsList[$elementId];

                    $loadedModuleTables[$loadedElement->dataResourceName]['language'] = $loadedElement->getCurrentLanguage();
                    $loadedModuleTables[$loadedElement->dataResourceName]['id'][] = $loadedElement->id;
                }
            }

            //preload all module data for all loaded elements - this is faster and more effective than lazy-loading
            if (count($idList) > 1) { // speed gain improbable if only one element is needed
                foreach ($loadedModuleTables as $resourceName => &$elementsInfo) {
                    if ($elementsInfo['language'] == 0) {
                        if ($rows = persistableCollection::getInstance($resourceName)
                            ->load(['id' => $elementsInfo['id']])
                        ) {
                            foreach ($rows as &$object) {
                                $loadedElements[$object->id]->setModuleDataObject($object, $elementsInfo['language']);
                            }
                        }
                    } else {
                        if ($rows = persistableCollection::getInstance($resourceName)->load(
                            [
                                'id' => $elementsInfo['id'],
                                'languageId' => $elementsInfo['language'],
                            ]
                        )
                        ) {
                            foreach ($rows as &$object) {
                                $loadedElements[$object->id]->setModuleDataObject($object, $elementsInfo['language']);
                            }
                        }
                    }
                }
            }

            if (isset($this->elementsList[$parentElementId]) && ($parentObject = $this->elementsList[$parentElementId])) {
                foreach ($idList as $positionItem) {
                    if (isset($loadedElements[$positionItem])) {
                        $parentObject->childrenList[] = $this->elementsList[$positionItem];
                    }
                }
            }

            foreach ($loadedElements as &$element) {
                $this->performAction($element);
            }
            return $loadedElements; //return array of loaded elements
        } else {
            return $loadedElements;
        }
    }

    /**
     * Creates empty non-initialized structure element object for provided type
     *
     * @param $type
     * @return bool|structureElement
     */
    protected function getElementInstance($type)
    {
        $newElement = false;
        $className = $type . 'Element';
        if (class_exists($className, true)) {
            $newElement = new $className();
            if ($newElement instanceof DependencyInjectionContextInterface) {
                $this->instantiateContext($newElement);
            }
        } else {
            $this->logError('Class "' . $className . '" is missing');
        }
        return $newElement;
    }

    /**
     * @param persistableObject $structureObject
     * @param int $parentId
     * @return bool|structureElement
     */
    protected function manufactureElement($structureObject, $parentId)
    {
        $id = $structureObject->id;
        if (isset($this->elementsList[$id]) && is_object($this->elementsList[$id])) {
            return $this->elementsList[$id];
        }

        $type = $structureObject->structureType;

        if ($manufacturedElement = $this->manufactureElementsObject($id, $type, $parentId)) {
            $manufacturedElement->setStructureDataObject($structureObject);
            $this->generateStructureInfo($manufacturedElement, $parentId);
            if (strtolower($this->requestedPathString) == strtolower($manufacturedElement->structurePath)) {
                $manufacturedElement->final = true;
            }
            $this->elementsList[$manufacturedElement->id] = $manufacturedElement;

            return $this->elementsList[$manufacturedElement->id];
        } else {
            $this->elementsList[$id] = false;
            return false;
        }
    }

    /**
     * @param $id
     * @param $type
     * @param $parentElementId
     * @return bool|structureElement
     */
    protected function manufactureElementsObject($id, $type, $parentElementId)
    {
        //todo: update and simplify privileges check
        if ($this->privilegeChecking) {
            $elementPrivileges = $this->privilegesManager->compileElementPrivileges($id, $parentElementId);
        }
        $result = false;
        if (!$this->privilegeChecking || isset($elementPrivileges[$type])) {
            if ($newElement = $this->getElementInstance($type)) {
                if (isset($this->elementsList[$parentElementId])) {
                    $newElement->setCurrentParentElementId($parentElementId);
                }
                $newElement->actionName = $this->defineElementAction($id, $type, $newElement->defaultActionName);
                if (!$this->privilegeChecking || $this->privilegesManager->checkPrivilegesForAction(
                        $id,
                        $newElement->actionName,
                        $type
                    )
                ) {
                    $result = $newElement;
                }
            }
        }
        return $result;
    }

    /**
     * @param $id
     * @param $type
     * @param $defaultAction
     * @return null
     */
    protected function defineElementAction($id, $type, $defaultAction)
    {
        $actionName = null;
        if (isset($this->customActions[$id])) {
            $actionName = $this->customActions[$id];
        } elseif (isset($this->defaultActions[$type])) {
            $actionName = $this->defaultActions[$type];
        } else {
            $actionName = $defaultAction;
        }
        return $actionName;
    }

    /**
     * @param $element
     */
    public function regenerateStructureInfo($element)
    {
        if ($element->id != $this->getRootElementId()) {
            if ($parentElements = $this->getElementsParents($element->id)) {
                $currentParent = false;
                foreach ($parentElements as $parentElement) {
                    if ($parentElement->requested) {
                        $currentParent = $parentElement;
                        break;
                    }
                }
                if (!$currentParent) {
                    $currentParent = reset($parentElements);
                }
                if ($currentParent) {
                    $this->generateStructureInfo($element, $currentParent->id);
                }
            } else {
                $this->generateStructureInfo($element);
            }
        }
    }

    /**
     * @param structureElement $element
     * @param $parentElementId
     */
    protected function generateStructureInfo($element, $parentElementId = false)
    {
        if ($parentElementId && isset($this->elementsList[$parentElementId])) {
            if (!$element->hasActualStructureInfo() && $element->structureName == '') {
                $element->structurePath = $this->elementsList[$parentElementId]->structurePath . 'type:' . $element->structureType . '/';
                $element->URL = $this->elementsList[$parentElementId]->URL . 'type:' . $element->structureType . '/';
                if ($parentElementId == $this->getRootElementId() || strpos(
                        $this->requestedPathString,
                        $this->elementsList[$parentElementId]->structurePath
                    ) === 0
                ) {
                    $element->requested = true;
                }
            } else {
                $element->structurePath = $this->elementsList[$parentElementId]->structurePath . $element->structureName . '/';
                $element->URL = $this->elementsList[$parentElementId]->URL . $element->structureName . '/';
            }
        } else {
            $element->structurePath = '';
            $element->URL = $this->rootURL;
        }

        if ($element->structurePath != "") {
            $element->level = $this->elementsList[$parentElementId]->level + 1;
            if (strpos($this->requestedPathString, $element->structurePath) === 0) {
                $element->requested = true;
            }
        } else {
            $element->level = 0;
            $element->requested = true;
        }
        if (strtolower($this->requestedPathString) == strtolower($element->structurePath)) {
            $element->final = true;
        }
    }

    /**
     * This method updates all indexed information about newly created and then persisted element.
     *
     * @param string $originalId - temporary ID in string form (like 'type:element/action:actionName')
     * @param int $newId - new ID from a database
     */
    public function reRegisterElement($originalId, $newId)
    {
        if (isset($this->elementsParents[$originalId])) {
            $this->elementsParents[$newId] = $this->elementsParents[$originalId];
            unset($this->elementsParents[$originalId]);
        }
        if (isset($this->elementsList[$originalId])) {
            $this->elementsList[$newId] = $this->elementsList[$originalId];
            unset($this->elementsList[$originalId]);
        }

        $this->privilegesManager->reRegisterElement($originalId, $newId);
        $this->linksManager->reRegisterElement($originalId, $newId);
        if ($parentElements = $this->getElementsParents($newId)) {
            foreach ($parentElements as &$parentElement) {
                $parentElement->childrenList[] = $this->elementsList[$newId];
            }
        }
    }

    /**
     * Searches and returns first structure element with assigned marker
     *
     * @param string $marker - element's marker to search for
     * @param int|null $parentElementId - restriction by parent id
     * @return structureElement|bool
     */
    public function getElementByMarker($marker, $parentElementId = null)
    {
        if ($parentElementId === null) {
            $cacheParentElementId = $this->getRootElementId();
        } else {
            $cacheParentElementId = $parentElementId;
        }

        if (!isset($this->cachedMarkers[$cacheParentElementId][$marker])) {
            $searchFields = ['marker' => $marker];
            $dataCollection = $this->elementsDataCollection->load($searchFields);
            foreach ($dataCollection as &$dataElement) {
                if (!$parentElementId || $this->checkElementInParent($dataElement->id, $parentElementId)) {
                    $this->cachedMarkers[$cacheParentElementId][$marker] = $this->getElementById($dataElement->id, $parentElementId);
                    break;
                }
            }
        }
        if (isset($this->cachedMarkers[$cacheParentElementId]) && isset($this->cachedMarkers[$cacheParentElementId][$marker])) {
            return $this->cachedMarkers[$cacheParentElementId][$marker];
        }
        return false;
    }

    public function checkElementInParent($id, $parentId)
    {
        $parentFound = false;
        if ($id == $parentId) {
            $parentFound = true;
        } else {
            if ($dataCollection = $this->linksManager->getElementsLinks($id, $this->getPathSearchAllowedLinks(), 'child', false)) {
                foreach ($dataCollection as &$dataObject) {
                    if ($dataObject->parentStructureId == $parentId) {
                        $parentFound = true;
                        break;
                    }
                }

                if (!$parentFound) {
                    foreach ($dataCollection as &$dataObject) {
                        if ($parentFound = $this->checkElementInParent($dataObject->parentStructureId, $parentId)) {
                            break;
                        }
                    }
                }
            }
        }
        return $parentFound;
    }

    /**
     * @param int $id
     * @param int|null $parentId
     * @param bool $directlyToParent
     * @return bool|structureElement
     */
    public function getElementById($id, $parentId = null, $directlyToParent = false)
    {
        if ($id) {
            if (isset($this->elementsList[$id])) {
                return $this->elementsList[$id];
            }
            if ($directlyToParent) {
                $this->loadElementsToParent([$id], $parentId);
            } else {
                $this->loadFromShortestPath($id, $parentId);
            }
            if (!empty($this->elementsList[$id])) {
                return $this->elementsList[$id];
            }
        }
        return false;
    }

    protected function loadFromCache($id, $parentElementId)
    {
        /**
         * @var structureElement $element
         */
        if ($element = $this->cache->get($id . ':e')) {
            if ($element instanceof DependencyInjectionContextInterface) {
                $this->instantiateContext($element);
            }

            if ($this->privilegeChecking) {
                $elementPrivileges = $this->privilegesManager->compileElementPrivileges($id, $parentElementId);
            }
            $type = $element->structureType;
            if (!$this->privilegeChecking || isset($elementPrivileges[$type])) {
                if (isset($this->elementsList[$parentElementId])) {
                    $element->setCurrentParentElementId($parentElementId);
                }
                $element->actionName = $this->defineElementAction($id, $type, $element->defaultActionName);
                if (!$this->privilegeChecking ||
                    $this->privilegesManager->checkPrivilegesForAction(
                        $id,
                        $element->actionName,
                        $type
                    )
                ) {
                    $this->generateStructureInfo($element, $parentElementId);
                    $this->elementsList[$element->id] = $element;

                    $this->performAction($element);
                    return $element;
                }
            }
        }
        return false;
    }

    /**
     * @param $id
     * @param int|null $parentId
     */
    protected function loadFromShortestPath($id, $parentId = null)
    {
        if ($id == $this->getRootElementId()) {
            $this->loadRootElement($id);
        }
        if (!$parentId) {
            $parentId = $this->elementPathRestrictionId;
        }
        if ($shortestChain = $this->findShortestParentsChain($id, $parentId)) {
            $parentId = end($shortestChain);
            while ($id = prev($shortestChain)) {
                //if parent element was never loaded let's ensure it's loaded now
                if (!isset($this->elementsList[$parentId])) {
                    $this->getElementById($parentId);
                }
                if (isset($this->elementsList[$parentId])) {
                    if (!isset($this->elementsList[$id])) {
                        if ($this->loadElementsToParent([$id], $parentId)) {
                            $parentId = $id;
                        } else {
                            break;
                        }
                    } else {
                        //if there were not enough privileges, then $this->elementsList[$id]==false
                        if ($this->elementsList[$id]) {
                            $parentId = $id;
                        } else {
                            break;
                        }
                    }
                } else {
                    $this->logError('ensureElementAvailability unpredicted problem. id:' . $id . ' parentId:' . $parentId);
                }
            }
        }
    }

    /**
     * This is recursive method to calculate the quickest/shortest way to load element within it's possible parent chains
     *
     * @param $id - target element id or current recursion level element id
     * @param null $withinParentId - if some parent id should strictly be in the chain, then it can be restricted with this parameter
     * @param int $points - current chain points: the smaller value == the shorter
     * @param array $chainElements - chain elements holder
     * @return array|bool
     */
    protected function findShortestParentsChain(
        $id,
        $withinParentId = null,
        &$points = 0,
        $chainElements = []
    ) {
        //if we are searching parent within itself then we will get nothing. we should not restrict parent within itself
        if ($withinParentId == $id) {
            $withinParentId = null;
        }
        //in case we don't have root element loaded we should check it as well
        if ($id == $this->rootElementId) {
            return [$id];
        }

        $key = 'ch:' . $this->languagesManager->getCurrentLanguageId() . ':p' . $withinParentId;
        if ($cachedChain = $this->cache->get($id . ":" . $key)) {
            return $cachedChain;
        }
        if (isset($this->shortestChains[$id][$withinParentId])) {
            return $this->shortestChains[$id][$withinParentId];
        }
        $this->shortestChains[$id][$withinParentId] = false;
        $shortestChainPointer = &$this->shortestChains[$id][$withinParentId];
        $chainElements[$id] = true;

        if ($parentLinks = $this->linksManager->getElementsLinks($id, $this->getPathSearchAllowedLinks(), 'child')) {
            $parentIds = [];
            foreach ($parentLinks as $parentLink) {
                $parentIds[] = $parentLink->parentStructureId;
            }
            //check all parent routes
            $bestPoints = false;
            foreach ($parentIds as $parentId) {
                if (!isset($chainElements[$parentId])) {
                    $newPoints = $points;
                    if ($withinParentId != $parentIds) {
                        if (!empty($this->elementsList[$parentId])) {
                            if (!$this->elementsList[$parentId]->requested) {
                                $newPoints += 2;
                            } else {
                                $newPoints += 1;
                            }
                        } else {
                            $newPoints += 3;
                        }
                    }

                    if ($chain = $this->findShortestParentsChain(
                        $parentId,
                        $withinParentId,
                        $newPoints,
                        $chainElements
                    )
                    ) {
                        if ($newPoints < $bestPoints || ($bestPoints === false)) {
                            if (!$withinParentId || in_array($withinParentId, $chain)) {
                                $bestPoints = $newPoints;
                                $shortestChainPointer = $chain;
                            }
                        }
                    }
                }
                if ($shortestChainPointer) {
                    $points = $bestPoints;
                    if (reset($shortestChainPointer) != $id) {
                        array_unshift($shortestChainPointer, $id);
                    }
                }
            }
        }
        $this->setElementCacheKey($id, $key, $shortestChainPointer, $this->cacheLifeTime * 2);
        return $shortestChainPointer;
    }

    /**
     * @param $idList
     * @param bool $parentElementId
     * @param bool $directlyToParent
     * @return structureElement[]
     */
    public function getElementsByIdList($idList, $parentElementId = false, $directlyToParent = false)
    {
        $elementsList = [];
        if ($idList) {
            if (!$parentElementId) {
                $parentElementId = $this->getRootElementId();
            }
            if ($directlyToParent) {
                if ($allowedElements = $this->privilegesManager->getAllowedElements($parentElementId, $idList)) {
                    // load the children elements from the storage and return them
                    $elementsList = $this->loadElementsToParent($idList, $parentElementId, $allowedElements);
                }
            } else {
                foreach ($idList as $id) {
                    if ($element = $this->getElementById($id, $parentElementId)) {
                        $elementsList[] = $element;
                    }
                }
            }
        }

        return $elementsList;
    }

    /**
     * @param structureElement $element
     * @return string
     */
    public function checkStructureName($element)
    {
        $elementId = $element->id;
        $currentName = trim($element->structureName);
        if (!$currentName) {
            $currentName = $element->structureType . $element->id;
        }
        $allowedTypes = $this->getPathSearchAllowedLinks();
        /**
         * @var Connection $db
         */
        $db = $this->getService('db');
        $parentIds = false;
        if ($records = $db->table('structure_links')
            ->select('parentStructureId')
            ->where('childStructureId', '=', $elementId)
            ->whereIn('type', $allowedTypes)->get()) {
            $parentIds = array_column($records, 'parentStructureId');
        }
        $newName = $currentName;
        if ($parentIds) {

            $query = $db->table('structure_elements')
                ->select('structureName')
                ->where('structureName', 'like', $currentName . '%')
                ->whereIn('id', function ($subQuery) use ($elementId, $allowedTypes, $parentIds) {
                    $subQuery->from('structure_links')
                        ->select('childStructureId')
                        ->where('childStructureId', '!=', $elementId)
                        ->whereIn('type', $allowedTypes)
                        ->whereIn('parentStructureId', $parentIds);
                });

            if ($rows = $query->get()) {
                $usedNames = array_column($rows, 'structureName');
                $currentNumber = 1;
                while (in_array(mb_strtolower($newName), $usedNames)) {
                    $newName = $currentName . $currentNumber;
                    $currentNumber++;
                }
            }
        }
        return $newName;
    }

    /**
     * Returns the ID number of first element with assigned marker.
     * Doesn't check the user privileges or manufacture the object itself, only queries the number in database
     *
     * @param string $marker
     * @return int|bool
     */
    public function getElementIdByMarker($marker)
    {
        $elementId = false;
        $collection = persistableCollection::getInstance('structure_elements');

        $columns = ['id'];

        $conditions = [];
        $conditions[] = [
            'column' => 'marker',
            'action' => '=',
            'argument' => $marker,
        ];

        $result = $collection->conditionalLoad($columns, $conditions, [], 1);
        foreach ($result as &$row) {
            $elementId = $row['id'];
            break;
        }
        return $elementId;
    }

    /**
     * @param $pathSearchLinksBlacklist
     *
     * @deprecated - delete in 04.2021
     */
    public function setPathSearchLinksBlacklist($pathSearchLinksBlacklist)
    {
        $this->logError('Deprecated method used setPathSearchLinksBlacklist');
    }

    /**
     * @return string[]
     *
     * @deprecated - delete in 04.2021
     */
    protected function getPathSearchLinksBlackList()
    {
        $this->logError('Deprecated method used getPathSearchLinksBlackList');
        return [];
    }

    /**
     * @return mixed
     */
    public function getPathSearchAllowedLinks()
    {
        return $this->pathSearchAllowedLinks;
    }

    /**
     * @param mixed $pathSearchAllowedLinks
     */
    public function setPathSearchAllowedLinks($pathSearchAllowedLinks)
    {
        $this->pathSearchAllowedLinks = $pathSearchAllowedLinks;
    }

    public function setRootUrl($rootUrl)
    {
        $this->rootURL = $rootUrl;
    }

    public function setRootElementId($rootElementId)
    {
        $this->rootElementId = $rootElementId;
        $this->rootElementMarker = null;
    }

    public function getRootElementId()
    {
        if ($this->rootElementId === null) {
            if ($this->rootElementMarker !== null) {
                $this->rootElementId = $this->getElementIdByMarker($this->rootElementMarker);
            } else {
                $this->rootElementId = false;
            }
        }
        return $this->rootElementId;
    }

    /**
     * @return mixed
     */
    public function getRootElementMarker()
    {
        return $this->rootElementMarker;
    }

    /**
     * @param mixed $rootElementMarker
     */
    public function setRootElementMarker($rootElementMarker)
    {
        $this->rootElementMarker = $rootElementMarker;
        $this->rootElementId = null;
    }

    public function setPrivilegeChecking($enabled)
    {
        $this->privilegeChecking = $enabled;
    }

    public function getPrivilegeChecking()
    {
        return $this->privilegeChecking;
    }

    public function getDeniedCopyLinkTypes()
    {
        return $this->deniedCopyLinkTypes;
    }

    public function setDeniedCopyLinkTypes($deniedCopyLinkTypes)
    {
        $this->deniedCopyLinkTypes = (array)$deniedCopyLinkTypes;
    }

    public function setElementPathRestrictionId($id)
    {
        $this->elementPathRestrictionId = $id;
    }

    protected function setElementCacheKey($id, $key, $value, $lifeTime)
    {
        $this->cache->set($id . ':' . $key, $value, $lifeTime);
        $this->registerElementCacheKey($id, $id . ':' . $key);
    }

    protected function registerElementCacheKey($id, $key)
    {
        if (!($keys = $this->cache->get($id . ':k'))) {
            $keys = [];
        }
        $keys[$key] = 1;
        $this->cache->set($id . ':k', $keys, 3600 * 24);
    }

    public function clearElementCache($id)
    {
        if ($keys = $this->cache->get($id . ':k', true)) {
            foreach ($keys as $key => $val) {
                $this->cache->delete($key);
            }
        }
    }
}
