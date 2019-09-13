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
            'originalName' => 'originalName',
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
            'code' => 'code',
            'languageId' => 'languageId',
            'price' => 'price',
            'oldPrice' => 'oldPrice',
            'discount' => function ($element) {
                /**
                 * @var productElement $element
                 */
                if ($discountElement = $element->getOldPrice()) {
                    return (int)$element->getDiscountPercent() . '%';
                }
                return '';
            },
            'showincategory' => 'showincategory',
            'brandId' => 'brandId',
            'brand' => function ($element) {
                /**
                 * @var productElement $element
                 */
                if ($brandElement = $element->getBrandElement()) {
                    return $brandElement->getTitle();
                }
                return '';
            },
            'purchaseCount' => 'purchaseCount',
            'inactive' => 'inactive',
            'color' => 'color',
            'deliveryStatus' => 'deliveryStatus',
            'availabilityStatus' => 'availabilityStatus',
            'importCode' => 'importCode',
            'deliveryPriceType' => 'deliveryPriceType',
            'importOrigin' => 'importOrigin',
            'importId' => 'importId',
            'metaTitle' => 'metaTitle',
            'metaDescription' => 'metaDescription',
            'availability' => function ($element) {
                /**
                 * @var productElement $element
                 */
                $translationsManager = $element->getService('translationsManager');
                $availabilityStatus = $translationsManager->getTranslationByName('product.' . $element->availability);

                $elementAvailability = [];
                $elementAvailability[$element->availability] = $availabilityStatus;
                return $elementAvailability;
            },
            'quantity' => 'quantity',
            'canonicalUrl' => 'canonicalUrl',
            'metaDenyIndex' => 'metaDenyIndex',
            'minimumOrder' => 'minimumOrder',
            'lastPurchaseDate' => 'lastPurchaseDate',
            'importPrice' => 'importPrice',
            'h1' => 'h1',
            'qtFromConnectedCategories' => 'qtFromConnectedCategories',
            'connectedProductCategories' => 'getProductConnectedCategories',
            'discountAmount' => function ($element) {
                /**
                 * @var productElement $element
                 */
                return ($element->getDiscountAmount(false, true) > 0) ? $element->getDiscountAmount(false, true) : false;
            },

            'deliveryTypesInfo' => function ($element) {
                /**
                 * @var productElement $element
                 */

                $deliveryTypesInfo = $element->getDeliveryTypesInfo();
                $deliveryInfo = [];
                foreach ($deliveryTypesInfo as $key => $value) {
                    $deliveryInfo[$value['id']] = ['title' => $value['title'], 'minPrice' => $value['minPrice'], 'maxPrice' => $value['maxPrice']];
                }
                return $deliveryInfo;
            },


            'title_ga' => function ($element) {
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
            'amountFilter' => 'getAmountSelectionOptions',
            'parametersGroups' => 'getGroupedParametersInfo',
            'iconsInfo' => 'getIconsInfo',
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
                 * @var DataResponseConverter $scope
                 */
                return $scope->htmlToPlainText($element->introduction);
            },
            'contentText' => function ($element, $scope) {
                /**
                 * @var DataResponseConverter $scope
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
            'search' => [
                'id',
                'searchTitle',
                'url',
                'structureType',
                'introductionText',
            ],
            'list' => [
                'id',
                'url',
                'price',
                'title',
                'category',
                'brand',
                'title_ga',
                'category_ga',
                'brand_ga',
                'oldPrice',
                'iconsInfo',
                'image',
                'originalName',
                'code',
            ],
            'detailed' => [
                'id',
                'price',
                'title',
                'category',
                'brand',
                'title_ga',
                'category_ga',
                'brand_ga',
                'oldPrice',
                'parametersGroups',
                'selectionsPricings',
                'selectionsOldPricings',
                'selectionsImages',
            ],
        ];
    }
}