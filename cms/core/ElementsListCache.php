<?php

class ElementsListCache
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var string
     */
    private $cacheId;

    /**
     * @var string
     */
    private $cacheKey;
    /**
     * @var int
     */
    private $cacheLifeTime;
    /**
     * @var structureManager
     */
    private $structureManager;
    private $idList;
    /**
     * @var structureElement[]
     */
    private $elements;

    /**
     * @param Cache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param mixed $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * @param int $cacheLifeTime
     */
    public function setCacheLifeTime($cacheLifeTime)
    {
        $this->cacheLifeTime = $cacheLifeTime;
    }

    /**
     * @param int $cacheId
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
    }

    /**
     * @param structureManager $structureManager
     */
    public function setStructureManager($structureManager)
    {
        $this->structureManager = $structureManager;
    }

    public function load()
    {
        if ($this->elements === null) {
            $this->elements = [];
            if ($value = $this->cache->get($this->cacheId . ':' . $this->cacheKey)) {
                $this->idList = $value;
            } else {
                $this->idList = [];
            }
            foreach ($this->idList as $id) {
                if ($element = $this->structureManager->getElementById($id)) {
                    $this->elements[] = $element;
                }
            }
        }

        return $this->elements;
    }

    public function save($elements)
    {
        $this->elements = $elements;
        $this->idList = [];
        foreach ($this->elements as $element) {
            if ($element instanceof structureElement) {
                $id = $element->getId();
                $this->registerElementCacheKey($id, $this->cacheId . ':' . $this->cacheKey);
                $this->idList[] = $id;
            }
        }
        $this->registerElementCacheKey($this->cacheId, $this->cacheId . ':' . $this->cacheKey);
        $this->cache->set($this->cacheId . ':' . $this->cacheKey, $this->idList, $this->cacheLifeTime);
    }

    public function loaded()
    {
        return !empty($this->elements);
    }

    protected function registerElementCacheKey($id, $key)
    {
        if (!($keys = $this->cache->get($id . ':k'))) {
            $keys = [];
        }
        $keys[$key] = 1;
        $this->cache->set($id . ':k', $keys, 3600 * 24);
    }
}