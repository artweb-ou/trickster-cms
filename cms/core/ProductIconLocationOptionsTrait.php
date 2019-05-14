<?php

trait ProductIconLocationOptionsTrait
{
    public $productIconLocationTypes = [
        1 => 'loc_top_left',
        2 => 'loc_top_right',
        3 => 'loc_bottom_left',
        4 => 'loc_bottom_right',
    ];

    /**
     * @return array
     */
    public function productIconLocationOptionsList()
    {
        return $this->productIconLocationTypes;
    }


}