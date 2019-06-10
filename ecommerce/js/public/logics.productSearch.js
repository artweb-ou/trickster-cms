window.productSearchLogics = new function() {
    var initComponents = function() {
        var elements = _('.productsearch');
        for (var i = 0; i < elements.length; i++) {
            new ProductSearchComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};