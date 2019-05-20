<?php

trait ProductIconRoleOptionsTrait
{
    /**
     * @var array
     */
    public $productIconRoleTypes = [
        'role_simple',
        'role_date',
        'role_general_discount',
        'role_availability',
        'role_by_parameter',
    ];

    /**
     * @return array
     */
    public function productIconRoleOptionsList()
    {
        return $this->productIconRoleTypes;
    }


}