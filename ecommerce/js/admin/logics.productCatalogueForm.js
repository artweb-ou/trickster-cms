window.productCatalogueFormLogics = new function() {
    var initComponents = function() {
        var element = _('.productcatalogue_form')[0];
        if (element) {
            new ProductCatalogueFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};