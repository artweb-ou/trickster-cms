window.productLogics = new function() {
    var self = this;
    var productsIndex = {};
    var productsListsIndex = {};
    var productsListComponents = [];
    var initLogics = function() {
        if (typeof window.productsLists !== 'undefined') {
            importProductsLists(window.productsLists);
        }
        trackImpressions(productsLists);
    };
    var initComponents = function() {
        var elements, i;
        elements = document.querySelectorAll('.productslist_component');
        for (i = 0; i < elements.length; i++) {
            var id = elements[i].dataset.id;
            if (typeof productsListsIndex[id] !== 'undefined') {
                var productsListComponent = new ProductsListComponent(elements[i]);
                productsListComponents.push(productsListComponent);
            }
        }
        elements = document.querySelectorAll('.product_details');
        for (i = 0; i < elements.length; i++) {
            new ProductDetailsComponent(elements[i]);
        }
    };
    var importProductsLists = function(data) {
        var productsList;
        for (var i = 0; i < data.length; i++) {
            var id = data[i].id;
            if (typeof productsListsIndex[id] === 'undefined') {
                productsList = new ProductsList(self);
                productsListsIndex[id] = productsList;
            } else {
                productsList = productsListsIndex[id];
            }
            productsList.importData(data[i]);
        }
    };
    this.getProduct = function(id) {
        if (typeof productsIndex[id] !== 'undefined') {
            return productsIndex[id];
        }
        return false;
    };
    this.getProductsList = function(id) {
        if (typeof productsListsIndex[id] !== 'undefined') {
            return productsListsIndex[id];
        }
        return false;
    };
    var trackImpressions = function(productsLists) {
        for (var i = 0; i < productsLists.length; i++) {
            tracking.impressionTracking(productsLists[i].products, productsLists[i].title);
        }
    };
    var receiveData = function(responseStatus, requestName, responseData) {
        if (requestName === 'ajaxProductsList') {
            if (responseStatus === 'success' && responseData) {
                if (typeof responseData.productsList !== 'undefined') {
                    importProductsLists([responseData.productsList]);
                }
            } else {
                controller.fireEvent('ajaxSearchResultsFailure', responseData);
            }
        }
    };
    this.requestProductsListData = function(productsListId, page, filters) {
        var reqUrl = '/ajaxProductsList/';
        var parameters = {
            'listElementId': productsListId,
            'page': page,
        };
        if (typeof filters !== 'undefined') {
            parameters['filters'] = filters;
        }
        var request = new JsonRequest(reqUrl, receiveData, 'ajaxProductsList', parameters);
        request.send();
    };
    this.importProduct = function(product) {
        productsIndex[product.getId()] = product;
    };

    controller.addListener('initLogics', initLogics);
    controller.addListener('initDom', initComponents);
};

window.ProductsList = function() {
    var self = this;
    this.id = null;
    this.title = null;
    this.url = null;
    this.filteredProductsAmount = 0;
    this.filterLimit = 0;
    this.currentPage = 1;
    var productsByPages = {};
    var productsIndex = {};

    this.importData = function(data) {
        self.id = data.id;
        self.url = data.url;
        self.filteredProductsAmount = data.filteredProductsAmount;
        self.filterLimit = data.filterLimit;
        self.currentPage = data.currentPage;

        if (typeof data.title != 'undefined') {
            self.title = data.title;
        }
        if (typeof data.products != 'undefined') {
            productsByPages[self.currentPage] = [];
            for (var i = 0; i < data.products.length; i++) {
                var product = new Product();
                product.importData(data.products[i]);

                productsByPages[self.currentPage].push(product);
                productsIndex[product.getId()] = product;
                window.productLogics.importProduct(product);
            }
        }
        controller.fireEvent('productsListUpdated', self.id);
    };
    this.changePage = function(newPageNumber) {
        if (self.currentPage !== newPageNumber) {
            productLogics.requestProductsListData(self.id, newPageNumber);
        }
    };
    this.getCurrentPageProducts = function() {
        if (typeof productsByPages[self.currentPage] !== 'undefined') {
            return productsByPages[self.currentPage];
        }
        return false;
    };
};
window.Product = function() {
    var self = this;
    this.title = null;
    var data = data;

    this.getId = function() {
        if (data.id) {
            return data.id;
        }
        return false;
    };

    this.getName = function() {
        if (data.title_ga) {
            return data.title_ga;
        }
        return false;
    };

    this.getBrand = function() {
        if (data.brand_ga) {
            return data.brand_ga;
        }
        return false;
    };

    this.getCategory = function() {
        if (data.category_ga) {
            return data.category_ga;
        }
        return false;
    };

    this.getVariant = function() {
        if (data.variant) {
            return data.variant;
        }
        return false;
    };

    this.getPrice = function() {
        if (data.price) {
            return data.price;
        }
        return false;
    };

    this.getQuantity = function() {
        if (data.quantity) {
            return data.quantity;
        }
        return false;
    };

    this.getCoupon = function() {
        if (data.coupon) {
            return data.coupon;
        }
        return false;
    };

    this.getPosition = function() {
        if (data.position) {
            return data.position;
        }
        return false;
    };

    this.importData = function(newData) {
        data = newData;
        self.title = data.title;
    };
};