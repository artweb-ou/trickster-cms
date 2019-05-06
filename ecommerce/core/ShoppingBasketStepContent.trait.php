<?php

trait ShoppingBasketStepContentTrait
{
    public function getValidators($formData = [])
    {
        return [];
    }

    public function useCustomFields() {
        return false;
    }
}