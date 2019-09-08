<?php

class ecommercePublicDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'ecommerce/css/public/';
        $this->templatesFolder = $tricksterPath . 'ecommerce/templates/public/';
        $this->imagesFolder = 'trickster/ecommerce/images/public/';
        $this->imagesPath = $tricksterPath . $this->imagesFolder;
        $this->javascriptUrl = $controller->baseURL . $pathsManager->getRelativePath('trickster') . 'ecommerce/js/public/';
        $this->javascriptPath = $tricksterPath . 'ecommerce/js/public/';
        $this->javascriptFiles = [
            'logics.dpd.js',
            'logics.post24.js',
            'logics.productDetails.js',
            'logics.productsFilter.js',
            'logics.shoppingBasket.js',
            'logics.smartPost.js',
            'logics.productSearch.js',
            'logics.brandsWidget.js',
            'logics.categoryShort.js',
            'logics.categoryThumbnail.js',
            'logics.productGallery.js',
            'logics.selectedProducts.js',
            'logics.productLimits.js',
            'logics.product.js',
            'component.ProductsList.js',
            'component.basketButton.js',
            'component.productDetails.js',
            'component.productsFilter.js',
            'component.shoppingBasketPaymentMethodTracking.js',
            'component.productSearch.js',
            'component.priceFilter.js',
            'component.brandsWidget.js',
            'component.categoryShort.js',
            'component.categoryThumbnail.js',
            'component.productGallery.js',
            'component.productShort.js',
            'component.productLimits.js',
            'component.selectedProductsColumn.js',
            'component.selectedProductsScroll.js',
            'component.shoppingBasketPopup.js',
            'component.shoppingBasketSelection.js',
            'component.ShoppingBasketDeliveriesSelector.js',
            'component.ShoppingBasketPromoCode.js',
            'component.ShoppingBasketSelectionCitiesSelector.js',
            'component.ShoppingBasketSelectionServices.js',
            'component.ShoppingBasketSelectionFormPost24Automate.js',
            'component.ShoppingBasketSelectionService.js',
            'component.ShoppingBasketSelectionFormDpdPoint.js',
            'component.ShoppingBasketSelectionFormSmartPostAutomate.js',
            'component.ShoppingBasketSelectionForm.js',
            'component.ShoppingBasketSelectionProducts.js',
            'component.ShoppingBasketSelectionFormFieldHelper.js',
            'component.ShoppingBasketSelectionProduct.js',
            'component.ShoppingBasketSelectionCountriesSelector.js',
            'component.ShoppingBasketSelectionFormField.js',
            'component.ShoppingBasketSelectionFormSmartPostRegion.js',
            'component.ShoppingBasketSelectionFormDpdRegion.js',
            'component.ShoppingBasketSelectionFormPost24Region.js',
            'component.ShoppingBasketTotals.js',
            'component.ShoppingBasketTotalsRow.js',
            'component.shoppingBasketStatus.js',
        ];
    }
}