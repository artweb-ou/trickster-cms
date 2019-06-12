window.ProductFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.product_form');
        for (var i = elements.length; i--;) {
            new ProductFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};