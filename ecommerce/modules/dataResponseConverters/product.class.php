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
                /**
                 * @var productElement $element
                 */
                if ($discountElement = $element->getOldPrice()) {
                    return (int)$element->getDiscountPercent() . '%';
                }
                return '';
            },
            'showincategory'            => 'showincategory',
            'brandId'                   => 'brandId',
            'brand'                => function ($element) {
                /**
                 * @var productElement $element
                 */
                if ($brandElement = $element->getBrandElement()) {
                    return $brandElement->getTitle();
                }
                return 'no';
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


// new new
            'title_ga' =>function ($element) {
                /**
                 * @var productElement $element
                 */
                return $element->getDefaultLanguageCustomProperty('title');
            },
            'category_ga' => function ($element) {
                /**
                 * @var productElement $element
                 */
                //   return $element->getDLCategoryTitle();
                return $element->getDefaultLanguageCustomProperty('title', 'category');
            },
            'brand_ga' => function ($element) {
                /**
                 * @var productElement $element
                 */
                return $element->getDefaultLanguageCustomProperty('title', 'brand');
            },

            'category' => function ($element) {
                /**
                 * @var productElement $element
                 */
                $categoryElement = $element->getRequestedParentCategory();
                return $categoryElement->title;
            },

            'selectionsImages' => function ($element) {
                /**
                 * @var productElement $element
                 */
                return $element->getOptionsImagesInfo();
            },

            // new END


            'introduction'     => 'introduction',
            'dateCreated'      => function ($element) {
                /**
                 * @var productElement $element
                 */
                return $element->getValue('dateCreated');
            },
            'dateModified'     => function ($element) {
                /**
                 * @var productElement $element
                 */
                return $element->getValue('dateModified');
            },
            'introductionText' => function ($element, $scope) {
                /**
                 * @var dataResponseConverter $scope
                 */
                return $scope->htmlToPlainText($element->introduction);
            },
            'contentText'      => function ($element, $scope) {
                /**
                 * @var dataResponseConverter $scope
                 */
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
                'brand',


                'title_ga',
                'category_ga',
                'brand_ga',
                'category',


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