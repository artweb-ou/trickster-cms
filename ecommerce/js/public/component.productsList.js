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
            var parameters = {
                'componentElement': elements[i],
                'baseURL': productsListData.url,
                'elementsCount': productsListData.filteredProductsAmount,
                'elementsOnPage': productsListData.filterLimit,
                'currentPage': productsListData.currentPage,
                'parameterName': 'page',
                'visibleAmount': 2,
                'callBack': pageChangeCallback,
            };
            var pagerComponent = window.pagerLogics.getPager(parameters);
            pagers.push(pagerComponent);
        }
    };
    var pageChangeCallback = function(pageNumber) {
        console.log(pageNumber);
    };
    init();
};