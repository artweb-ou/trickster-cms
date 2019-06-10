window.SelectedProductsFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.productsearch_form');
        for (var i = elements.length; i--;) {
            new ProductSearchFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};