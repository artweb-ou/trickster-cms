<?php

class productDataResponseConverter extends StructuredDataResponseConverter
{
    protected $defaultPreset = 'api';

    protected function getRelationStructure()
    {
        return [
            'id'                        => 'id',
            'title'                     => 'title',
            'searchTitle'               => 'getSearchTitle',
            'url'                       => 'getUrl',
            'structureType'             => 'structureType',
            'image'                     => 'image',
            'content'                   => 'content',

            // new
            'code'                      => 'code',
            'languageId'                => 'languageId',
            'price'                     => 'price',
            'oldPrice'                  => 'oldPrice',
            'discount'                  =>  function ($element) {
                if ($discountElement = $element->getOldPrice()) {
                    return (int)$element->getDiscountPercent() . '%';
                }
                return '';
            },
            'showincategory'            => 'showincategory',
            'brandId'                   => 'brandId',
            'brandTitle'                => function ($element) {
                if ($brandElement = $element->getBrandElement()) {
                    return $brandElement->title;
                }
                return '';
            },
            'purchaseCount'             => 'purchaseCount',
            'inactive'                  => 'inactive',
            'color'                     => 'color',
            'deliveryStatus'            => 'deliveryStatus',
            'availabilityStatus'        => 'availabilityStatus',
            'importCode'                => 'importCode',
            'deliveryPriceType'         => 'deliveryPriceType',
            'importOrigin'              => 'importOrigin',
            'importId'                  => 'importId',
            'metaTitle'                 => 'metaTitle',
            'metaDescription'           => 'metaDescription',
            'availability'              => 'availability',
            'quantity'                  => 'quantity',
            'canonicalUrl'              => 'canonicalUrl',
            'metaDenyIndex'             => 'metaDenyIndex',
            'minimumOrder'              => 'minimumOrder',
            'lastPurchaseDate'          => 'lastPurchaseDate',
            'importPrice'               => 'importPrice',
            'h1'                        => 'h1',
            'qtFromConnectedCategories' => 'qtFromConnectedCategories',

            // new END


            'introduction'     => 'introduction',
            'dateCreated'      => function ($element) {
                return $element->getValue('dateCreated');
            },
            'dateModified'     => function ($element) {
                return $element->getValue('dateModified');
            },
            'introductionText' => function ($element, $scope) {
                return $scope->htmlToPlainText($element->introduction);
            },
            'contentText'      => function ($element, $scope) {
                return $scope->htmlToPlainText($element->content);
            },
        ];

    }

    protected function getPresetsStructure()
    {
        return [
            'api' => [
                'id',
                'title',
                'dateCreated',
                'dateModified',
                'url',
                'image',
                'content',
                'introduction',
            ],
            'search' => [
                'id',
                'searchTitle',
                'url',
                'structureType',
                'introductionText',
            ],
            'detailed'    => [
                'id',
                'title',
                'dateCreated',
                'dateModified',
                'url',
                'image',
                'content',
                'introduction',

                // new
                'code',
                'languageId',
                'price',
                'oldPrice',
                'discount',
                'showincategory',
                'brandId',
                'brandTitle',
                'purchaseCount',
                'inactive',
                'color',
                'deliveryStatus',
                'availabilityStatus',
                'importCode',
                'deliveryPriceType',
                'importOrigin',
                'importId',
                'metaTitle',
                'metaDescription',
                'availability',
                'quantity',
                'canonicalUrl',
                'metaDenyIndex',
                'minimumOrder',
                'lastPurchaseDate',
                'importPrice',
                'h1',
                'qtFromConnectedCategories',
                // new END


            ],
        ];
    }
}