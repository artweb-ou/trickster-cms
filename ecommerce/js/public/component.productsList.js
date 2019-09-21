window.ProductsListComponent = function(componentElement) {
    var self = this;
    var id;
    var productsListData;
    var productsListElement;
    var filtersComponent;
    var amountTextElement;
    var productComponents = [];
    var pagers = [];

    var init = function() {
        initComponents();
    };

    var initComponents = function() {
        id = parseInt(componentElement.dataset.id, 10);
        if (productsListData = window.productLogics.getProductsList(id)) {
            if (productsListElement = componentElement.querySelector('.productslist_products')) {
                createProductComponents();
            }
            createProductsFilterComponent();
            createPagers();
            amountTextElement = componentElement.querySelector('.products_filter_amount');
            controller.addListener('productsListUpdated', updateHandler);
        }
    };

    var updateHandler = function(updatedId) {
        if (updatedId === id) {
            if (productsListData = window.productLogics.getProductsList(id)) {
                updatePagers();
                if (filtersComponent) {
                    filtersComponent.updateData(productsListData.getFilters());
                    filtersComponent.rebuildFilters();
                }
                if (amountTextElement) {
                    amountTextElement.innerHTML = translationsLogics.get('category.productsamount', {'s':productsListData.filteredProductsAmount});
                }
                renderProductsHtml();
                createProductComponents();
                controller.fireEvent('initLazyImages');
            }
        }
    };

    var pageChangeCallback = function(pageNumber) {
        productsListData.changePage(pageNumber);
    };

    var renderProductsHtml = function() {
        if (productsListElement) {
            while (productsListElement.firstChild) {
                productsListElement.removeChild(productsListElement.firstChild);
            }
            var html = '';
            var products = productsListData.getCurrentPageProducts();
            for (var i = 0; i < products.length; i++) {
                var templateName = 'product.' + productsListData.productsLayout + '.tpl';

                var data = {
                    'element': products[i],
                };
                html += smartyRenderer.fetch(templateName, data);
            }
            productsListElement.innerHTML = html;
        }
    };

    var updatePagers = function() {
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
    };

    var createPagers = function() {
        var elements, i;

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

    var createProductComponents = function() {
        if (productsListElement) {
            productComponents = [];
            var elements = productsListElement.querySelectorAll('.product_short');
            for (var i = 0; i < elements.length; i++) {
                var product = new ProductShortComponent(elements[i]);
                productComponents.push(product);
            }
        }
    };

    var createProductsFilterComponent = function() {
        var element = componentElement.querySelector('.products_filter');
        if (element) {
            filtersComponent = new ProductsFilterComponent(element, self);
            filtersComponent.updateData(productsListData.getFilters());
            filtersComponent.initFilters();
        }
    };

    this.changeFilterValue = function(type, value) {
        productsListData.changeFilter(type, value);
    };

    init();
};