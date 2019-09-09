window.ProductsListComponent = function(componentElement, productsListData) {
    var self = this;
    var productsListElement;
    var products = [];
    var pagers = [];
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
        elements = componentElement.querySelectorAll('.pager_block');
        for (i = 0; i < elements.length; i++) {
            var pagerComponent = window.pagerLogics.getPager(elements[i], productsListData.url, productsListData.filteredProductsAmount, productsListData.filterLimit, productsListData.currentPage, 'page', 2);
            pagers.push(pagerComponent);
        }
    };

    init();
};