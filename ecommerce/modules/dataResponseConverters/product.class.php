<?php

class productDataResponseConverter extends StructuredDataResponseConverter
{
    protected $defaultPreset = 'api';

    protected function getRelationStructure()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'searchTitle' => 'getSearchTitle',
            'url' => 'getUrl',
            'structureType' => 'structureType',
            'image' => 'image',
            'imageUrl' => function ($element) {
                /**
                 * @var productElement $element
                 */

                if ($element->image && $element->originalName) {
                    return $element->getCustomImageUrl('productGalleryImage', 'originalName');
                }
                return '';
            },
            'content' => 'content',

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
            'brand'                     => function ($element) {
                /**
                 * @var productElement $element
                 */
                if ($brandElement = $element->getBrandElement()) {
                    return $brandElement->getTitle();
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
        //    'availability'              => 'availability',
            'availability'              => function ($element) {
            /**
             * @var productElement $element
             */
                $translationsManager = $element->getService('translationsManager');
                $availabilityStatus =  $translationsManager->getTranslationByName('product.' . $element->availability);

                $elementAvailability = [];
                $elementAvailability[$element->availability] = $availabilityStatus;
                return $elementAvailability;
            },
            'quantity'                  => 'quantity',
            'canonicalUrl'              => 'canonicalUrl',
            'metaDenyIndex'             => 'metaDenyIndex',
            'minimumOrder'              => 'minimumOrder',
            'lastPurchaseDate'          => 'lastPurchaseDate',
//            'lastPurchaseDate'          => function ($element) {
//            /**
//             * @var productElement $element
//             */
//                return date('Y-m-d H:i:s',$element->lastPurchaseDate);
//            },
            'importPrice'               => 'importPrice',
            'h1'                        => 'h1',
            'qtFromConnectedCategories' => 'qtFromConnectedCategories',
// new new
            'connectedProductCategories'=> 'getProductConnectedCategories',
//            'connectedProductCategories'=> function($element) {
//                /**
//                 * @var productElement $element
//                 */
//                return $element->getProductConnectedCategories();
//            },
            'discountAmount'         =>  function($element) {
                /**
                 * @var productElement $element
                 */
                return ($element->getDiscountAmount(false, true) > 0) ? $element->getDiscountAmount(false, true) : false;
            },

            'deliveryTypesInfo'         =>  function($element) {
                /**
                 * @var productElement $element
                 */

                $deliveryTypesInfo = $element->getDeliveryTypesInfo();
                $deliveryInfo = [];
                foreach ($deliveryTypesInfo as $key=>$value) {
                    $deliveryInfo[$value['id']] = ['title'=>$value['title'], 'minPrice' => $value['minPrice'], 'maxPrice' => $value['maxPrice']];
                }
                return $deliveryInfo ;
            },


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
                return !empty($element->getOptionsImagesInfo()) ? $element->getOptionsImagesInfo() : false;
            },
            'selectionsPricings' => function ($element) {
                /**
                 * @var productElement $element
                 */
                $getSelectionsPricings = $element->getSelectionsPricings();
                return $getSelectionsPricings['selectionsPricings'];
            },
            'selectionsOldPricings' => function ($element) {
                /**
                 * @var productElement $element
                 */
                $getSelectionsPricings = $element->getSelectionsPricings();
                return $getSelectionsPricings['selectionsOldPricings'];
            },



            'parametersGroups' => 'getGroupedParametersInfo',
//            'parametersGroups' => function ($element) {
//                    /**
//                     * @var productElement $element
//                     */
//                    $parametersGroupsInfo = $element->getParametersGroupsInfo();
//                    $parametersInfo = [];
//                    foreach ($parametersGroupsInfo as $info) {
//                        $parametersList = [];
//                        foreach ($info['parametersList'] as $list) {
//                            $listOptions = [];
//                            foreach ($list['productOptions'] as $option) {
//                               $listOptions[$option['id']] = ['title' => $option['title'], 'value' => $option['value']];
//                            };
//
//                            $parametersList[$list['id']] = !empty($listOptions) ? ['title' => $list['title'], 'value' => isset($list['value']) ? $list['value'] : false, 'parameter' => $listOptions] : ['title' => $list['title'], 'value' => $list['value']];
//                        }
//                        $parametersInfo[$info['id']] = ['groupTitle' => $info['title'], 'parametersList'=>$parametersList];
//                    }
//                    return $parametersInfo;
//                },

            // new END


            'introduction' => 'introduction',
            'dateCreated' => function ($element) {
                /**
                 * @var productElement $element
                 */
                return $element->getValue('dateCreated');
            },
            'dateModified' => function ($element) {
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
            'contentText' => function ($element, $scope) {
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
                'imageUrl',
                'content',
                'introduction',

                // new
                'code',
                'languageId',
                'price',
                'oldPrice',
                'discount',

                'discountAmount',
                'showincategory',
                'brandId',
                'brand',


                'title_ga',
                'category_ga',
                'brand_ga',
                'category',

                'selectionsImages',
                'selectionsPricings',
                'selectionsOldPricings',


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

                'parametersGroups',
                'connectedProductCategories',
                'qtFromConnectedCategories',

                'deliveryTypesInfo',


            ],
        ];
    }
}