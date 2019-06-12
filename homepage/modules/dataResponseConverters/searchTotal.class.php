<?php

class searchTotalDataResponseConverter extends dataResponseConverter
{
    public function convert($data)
    {
        return (int)$data;
    }
}