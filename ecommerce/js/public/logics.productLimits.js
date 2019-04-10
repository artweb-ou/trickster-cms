window.productLimits = new function() {
    var initComponents = function() {
        var elements = _('.products_limit');
        for (var i = 0; i < elements.length; i++) {
            new ProductLimitsComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};