<?php

class IconsManager
{
    /**
     * @var structureManager
     */
    protected $structureManager;
    /**
     * @var Illuminate\Database\Capsule\Manager()
     */
    protected $db;
    /**
     * @var genericIconElement[]
     */
    protected $iconElements;

    /**
     * @param Illuminate\Database\Capsule\Manager() $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * @param mixed $structureManager
     */
    public function setStructureManager($structureManager)
    {
        $this->structureManager = $structureManager;
    }

    public function getElementIcons($id)
    {
    }

    public function getAllIcons()
    {
        if ($this->iconElements === null) {
            $allIconsIds = $this->db->table('module_generic_icon')->select('id')->distinct('id')->get();
            $allIconsIds = array_column($allIconsIds, 'id');
            if ($iconElements = $this->structureManager->getElementsByIdList($allIconsIds, null, true)) {
                $this->iconElements = $iconElements;
            }
        }
        return $this->iconElements;
    }
}