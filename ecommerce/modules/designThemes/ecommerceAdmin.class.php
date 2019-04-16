<?php

class ecommerceAdminDesignTheme extends designTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'ecommerce/css/admin/';
        $this->templatesFolder = $tricksterPath . 'ecommerce/templates/admin/';
        $this->imagesFolder = $tricksterPath . 'ecommerce/images/admin/';
        $this->imagesPath = $this->imagesFolder;
        $this->javascriptPath = $tricksterPath . 'ecommerce/js/admin/';
        $this->javascriptUrl = $controller->baseURL . $pathsManager->getRelativePath('trickster') . 'ecommerce/js/admin/';

        $this->javascriptFiles = [
            'logics.brandsListForm.js',
            'logics.campaignsListForm.js',
            'logics.deliveryTypeForm.js',
            'logics.discountForm.js',
            'logics.discountsListForm.js',
            'logics.importCalculationsRule.js',
            'logics.importForm.js',
            'logics.orderForm.js',
            'logics.orderProductForm.js',
            'logics.orders.js',
            'logics.paymentForm.js',
            'logics.productCatalogueForm.js',
            'logics.productForm.js',
            'logics.productGalleryProductForm.js',
            'logics.productImportForm.js',
            'logics.productImportTemplateColumn.js',
            'logics.productSearchForm.js',
            'logics.productSelectionForm.js',
            'logics.selectedDiscounts.js',
            'logics.selectedProductsForm.js',
            'logics.catalogueFilter.js',
            'logics.catalogueMassEditForm.js',
            'logics.salesStatistics.js',
            'logics.visitors.js',
            'component.brandsListForm.js',
            'component.campaignsListForm.js',
            'component.deliveryTypeForm.js',
            'component.discountForm.js',
            'component.discountsListForm.js',
            'component.importCalculationsRuleForm.js',
            'component.importForm.js',
            'component.orderForm.js',
            'component.orderProductForm.js',
            'component.ordersList.js',
            'component.paymentForm.js',
            'component.productCatalogueForm.js',
            'component.productForm.js',
            'component.productGalleryProductForm.js',
            'component.productImportForm.js',
            'component.productImportTemplateColumn.js',
            'component.productSearchForm.js',
            'component.productSelectionForm.js',
            'component.selectedDiscounts.js',
            'component.selectedProductsForm.js',
            'component.catalogueFilter.js',
            'component.catalogueMassEditForm.js',
            'component.salesStatistics.js',
        ];
    }
}