window.productsFilterLogics = new function() {
    var initComponents = function() {
        var elements = _('.products_filter');
        for (var i = elements.length; i--;) {
            new ProductsFilterComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};