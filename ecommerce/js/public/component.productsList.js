window.ProductsListComponent = function(componentElement, productsListData) {
    var self = this;
    var productsListElement;
    var products = [];
    var init = function() {
        initComponents();
    };
    var initComponents = function() {
        var elements, i;
        if (productsListElement = componentElement.querySelector('.productslist_products')) {
            elements = productsListElement.querySelectorAll('.product_short');
            for (i = 0; i < elements.length; i++) {
                var product = new ProductShortComponent(elements[i]);
                products.push(product);
            }
        }
    };

    init();
};