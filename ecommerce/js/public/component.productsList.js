window.ProductsListComponent = function(componentElement) {
    var id;
    var productsListData;
    var self = this;
    var productsListElement;
    var products = [];
    var pagers = [];
    var init = function() {
        initComponents();
    };
    var initComponents = function() {
        id = parseInt(componentElement.dataset.id, 10);
        if (productsListData = window.productLogics.getProductsList(id)) {
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
            controller.addListener('productsListUpdated', updateHandler);
        }
    };
    var updateHandler = function(updatedId) {
        if (updatedId === id) {
            if (productsListData = window.productLogics.getProductsList(id)) {
                for (var i = 0; i < pagers.length; i++) {
                    var parameters = {
                        'baseURL': productsListData.url,
                        'elementsCount': productsListData.filteredProductsAmount,
                        'elementsOnPage': productsListData.filterLimit,
                        'currentPage': productsListData.currentPage,
                        'parameterName': 'page',
                        'visibleAmount': 2,
                        'callBack': pageChangeCallback,
                    };
                    var pagerData = pagerLogics.getPagerData(parameters);
                    pagers[i].updateData(pagerData);
                }
                while (productsListElement.firstChild) {
                    productsListElement.removeChild(productsListElement.firstChild);
                }
                var html = '';
                var products = productsListData.getCurrentPageProducts();
                for (var i = 0; i < products.length; i++) {
                    var templateName = 'product.detailed2.tpl';

                    var data = {
                        'element': products[i],
                    };
                    html += smartyRenderer.fetch(templateName, data);
                }
                productsListElement.innerHTML = html;
            }
        }
    };
    var pageChangeCallback = function(pageNumber) {
        productsListData.changePage(pageNumber);
    };
    init();
};