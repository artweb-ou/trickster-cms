window.shopCatalogueLogics = new function() {
    var initComponents = function() {
        var elements = _('.shop_catalogue');
        for (var i = elements.length; i--;) {
            new ShopCatalogueComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};