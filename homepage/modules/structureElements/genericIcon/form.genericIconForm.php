<?php

class GenericIconFormStructure extends ElementForm
{
    use productsAvailabilityOptionsTrait;

    protected $formClass = 'genericicon_form';
    protected $structure = [];
    protected $structure_total_top = [
        'title' => [
            'type' => 'input.multi_language_text',
            'class' => 'genericicon_title',
        ],
        'image' => [
            'type' => 'input.multi_language_image',
            'class' => 'genericicon_icon',
        ],
        'iconWidth' => [
            'type' => 'input.multi_language_text',
            'inputType' => 'number',
        ],
        'iconLocation' => [
            'type' => 'select.index',
            'options' => [
                1 => 'loc_top_left',
                2 => 'loc_top_right',
                3 => 'loc_bottom_left',
                4 => 'loc_bottom_right',
            ],
         //   'translationGroup' => 'order', //'translationGroup' => 'admintranslation',
        ],
        'iconRole' => [
            'type' => 'select.index',
            'options' => [
                1 => 'role_default',
                2 => 'role_date',
                3 => 'role_general_discount',
                4 => 'role_availability',
                5 => 'role_by_parameter',
            ],
         //   'translationGroup' => 'order', //'translationGroup' => 'admintranslation',
        ],
    ];
    protected $structure_total_bottom = [
        'products' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedProducts',
            'class' => 'genericicon_form_productselect',
        ],
        'categories' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedCategoriesInfo',
            'class' => 'genericicon_form_categoryselect',
        ],
        'brands' => [
            'type' => 'select.universal_options_multiple',
            'method' => 'getConnectedBrands',
            'class' => 'genericicon_form_brandselect',
        ],
    ];

    protected $structure_date = [
        'startDate' => [
            'type' => 'input.date',
        ],
        'endDate' => [
            'type' => 'input.date',
        ],
        'days' => [
            'type' => 'input.text',
        ],
    ];

    protected $structure_product_avail = [
        'iconProductAvail' => [
            'type' => 'select.universal_options_multiple',
            'select_options' => 'productsAvailOptions',
            'translationGroup' => 'product', //'translationGroup' => 'admintranslation',
        ],
    ];


    public function getFormComponents(){
        $iconRole = $this->getElementProperty('iconRole');
        
        if ($iconRole == 2) { // 'role_date'
           return $this->structure_total_top + $this->structure_date + $this->structure_total_bottom;
        }
        elseif ($iconRole == 4) { // 'role_availability'
            $this->productsAvailOptions =  $this->getProductsAvailOptions(); // list in productsAvailabilityOptionsTrait
            return $this->structure_total_top + $this->structure_product_avail + $this->structure_total_bottom;
        }
        else{
            return $this->structure_total_top + $this->structure_total_bottom;
        }
    }
    public function getProductsAvailOptions()
    {
        $productsAvailOptions = $this->productsAvailOptions('',1);
        $assoProductsAvailOptions = [];
        foreach ($productsAvailOptions as $optionKey=>$optionValue) {
            $assoProductsAvailOptions[] = array('id'=> $optionKey, 'title'=> $optionValue);
        }
        return $assoProductsAvailOptions;
    }

}