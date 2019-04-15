<?php

class addProductShoppingBasket extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param shoppingBasketElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        /**
         * @var shoppingBasket $shoppingBasket
         */
        $shoppingBasket = $this->getService('shoppingBasket');
        $structureElement->shoppingBasket = $shoppingBasket;

        $productAmount = $controller->getParameter('productAmount');
        $productId = $controller->getParameter('productId');
        $responseStatus = "fail";
        $languagesManager = $this->getService('languagesManager');
        /**
         * @var productElement $productElement
         */
        $productElement = $structureManager->getElementById($productId, $languagesManager->getCurrentLanguageId());
        if ($productElement) {
            $options = [];
            $param = $controller->getParameter('productOptions');
            if ($param) {
                $options = explode(',', $param);
            }
            $everythingSelected = true;
            $selections = $productElement->getBasketSelectionsInfo();
            $variations = [];
            $variations_dl = [];
            $influentialOptions = [];
            $languageManager = $this->getService('languagesManager');
            $defaultLanguage = $languageManager->getDefaultLanguage('adminLanguages');
            foreach ($selections as $selection) {
                $selectedOption = '';
                $structureManager->getElementsByIdList([$selection['id']], false, true);
                $select = $structureManager->getElementById($selection['id']);
                foreach ($selection['productOptions'] as $option) {
                    if (in_array($option['id'], $options)) {
                        $variant = $structureManager->getElementById($option['id']);
                        $selectedOption = $option['id'];
                        $variations[] = $selection['title'] . ': ' . $option['title'];

                        $variations_dl[] = ($select?
                            $select->getValue('title',$defaultLanguage->id) : '' ). ': '
                            . ($variant? $variant->getValue('title', $defaultLanguage->id): '');
                        break;
                    }
                    if ($selection['influential']) {
                        $influentialOptions[] = $selectedOption;
                    }
                }
                if ($everythingSelected && $influentialOptions) {
                    $parametersPrice = $productElement->getPriceBySelectedOptions($influentialOptions);
                }
            }
            $productPrice = $parametersPrice ?: $productElement->price;
            if (is_numeric($productAmount) && is_numeric($productId) && ($everythingSelected || $controller->getParameter('productVariation'))) {
                $finalAmount = $shoppingBasket->getProductOverallQuantity($productId) + $productAmount;
                if ($productElement->isPurchasable($finalAmount)) {
                    $data = [];
                    $data['productId'] = $productElement->id;
                    $data['code'] = $productElement->code;
                    $data['title'] = $productElement->title;
                    $data['title_dl'] = $productElement->getValue('title', $defaultLanguage->id);
                    $data['price'] = $productPrice;
                    $data['emptyPrice'] = $productElement->isEmptyPrice();
                    $data['unit'] = $productElement->getUnit();
                    $data['image'] = $controller->baseURL . "image/type:basketProduct/id:" . $productElement->image . "/filename:" . $productElement->originalName;
                    $data['URL'] = $productElement->URL;
                    $data['vatIncluded'] = $productElement->vatIncluded;
                    $data['variation'] = $variations;
                    $data['variation_dl'] = $variations_dl;

                    $data['amount'] = $productAmount;
                    $data['minimumOrder'] = $productElement->minimumOrder;
                    $data['description'] = $productElement->description;

                    $categoryDeliveryExtraPrices = [];
                    $categories = $productElement->getConnectedCategories();
                    $deliveryPriceType = false;
                    foreach ($categories as &$category) {
                        $deliveryPriceType = $category->deliveryPriceType;
                        $pricesIndex = $category->getPricesIndex();
                        foreach ($pricesIndex as $deliveryTypeId => $regions) {
                            foreach ($regions as $regionId => $record) {
                                $categoryDeliveryExtraPrices[$deliveryTypeId][$regionId] = $record->price;
                            }
                        }
                    }

                    $pricesIndex = $productElement->getPricesIndex();
                    $productDeliveryExtraPrices = [];
                    foreach ($pricesIndex as $deliveryTypeId => $regions) {
                        foreach ($regions as $regionId => $record) {
                            $productDeliveryExtraPrices[$deliveryTypeId][$regionId] = $record->price;
                            if (!$regionId) {
                                if (isset($categoryDeliveryExtraPrices[$deliveryTypeId])) {
                                    foreach ($categoryDeliveryExtraPrices[$deliveryTypeId] as $key => $somePrice) {
                                        $categoryDeliveryExtraPrices[$deliveryTypeId][$key] = $record->price;
                                    }
                                }
                            }
                        }
                    }

                    if ($productElement->deliveryPriceType) {
                        $deliveryPriceType = $productElement->deliveryPriceType;
                    }

                    $data['deliveryPriceType'] = $deliveryPriceType;
                    $data['deliveryExtraPrices'] = [];
                    foreach ($productDeliveryExtraPrices as $deliveryTypeId => $regions) {
                        foreach ($regions as $regionId => $price) {
                            $categoryDeliveryExtraPrices[$deliveryTypeId][$regionId] = $price;
                        }
                    }
                    $data['deliveryExtraPrices'] = $categoryDeliveryExtraPrices;

                    $shoppingBasket->addProduct($data);
                    $responseStatus = "success";
//                    $productElement->logVisitorEvent('shoppingbasket_addition', ['amount' => $productAmount]);
                    $eventLogger = $this->getService('eventsLog');
                    $event = new Event();
                    $visitorManager = $this->getService('VisitorsManager');
                    $visitor = $visitorManager->getCurrentVisitor();
                    $event->setType('shoppingbasket_addition');
                    $event->setElementId($productElement->id);
                    $event->setVisitorId($visitor->id);
                    $eventLogger->saveEvent($event);
                    $event = new Event();
                    $visitorManager = $this->getService('VisitorsManager');
                    $visitor = $visitorManager->getCurrentVisitor();
                    $event->setType('shoppingbasket_addition');
                    $event->setElementId($productElement->id);
                    $event->setVisitorId($visitor->id);
                }
            }
        }
        /**
         * @var jsonRendererPlugin $renderer
         */
        $renderer = $this->getService('renderer');
        $renderer->assign('responseStatus', $responseStatus);
        $renderer->assignResponseData('shoppingBasketData', $structureElement->getElementData());
    }
}