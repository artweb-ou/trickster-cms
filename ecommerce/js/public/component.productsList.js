window.ProductsListComponent = function(componentElement) {
    var self = this;
    var products = [];
    var init = function() {
        initComponents();
    };
    var initComponents = function() {
        var elements, i;
        elements = componentElement.querySelectorAll('.product_short');
        for (i = 0; i < elements.length; i++) {
            var product = new ProductShortComponent(elements[i]);
            product.push(product);
        }
    };
    init();
};