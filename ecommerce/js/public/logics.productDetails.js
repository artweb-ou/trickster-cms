window.productDetailsLogics = new function() {
    var self = this;
    this.productDetails = false;
    var initComponents = function() {
        var elements = _('.product_details');
        for (var i = 0; i < elements.length; i++) {
            self.productDetails = new ProductDetailsComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};