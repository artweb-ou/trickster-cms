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
            var i;
            var uniqueValues = {};
            for (i = 0; i < filters.length; i++) {
                if (typeof (uniqueValues[filters[i][0]]) === 'undefined') {
                    uniqueValues[filters[i][0]] = [];
                }
                uniqueValues[filters[i][0]].push(filters[i][1]);
            }
            for (i in uniqueValues) {
                if (uniqueValues.hasOwnProperty(i)) {
                    parameters[i] = uniqueValues[i].join(',');
                }
            }
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
    this.filters = [];
    this.filteredProductsAmount = 0;
    this.filterLimit = 0;
    this.currentPage = 1;
    this.productsLayout = 'thumbnail';
    var productsByPages = {};
    var productsIndex = {};

    this.importData = function(data) {
        self.id = data.id;
        self.url = data.url;
        self.filteredProductsAmount = data.filteredProductsAmount;
        self.filterLimit = data.filterLimit;
        self.currentPage = data.currentPage;
        self.filters = data.filters;
        self.productsLayout = data.productsLayout;

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
            var filtersInfo = gatherFilterValues();
            productLogics.requestProductsListData(self.id, newPageNumber, filtersInfo);
        }
    };
    this.changeFilter = function(filterId, value) {
        var filtersInfo = gatherFilterValues(filterId, value);
        productLogics.requestProductsListData(self.id, 0, filtersInfo);
    };
    var gatherFilterValues = function(filterId, value) {
        var filtersInfo = [];
        var i, j;
        for (i = 0; i < self.filters.length; i++) {
            var filter = self.filters[i];
            if (filter.id == filterId) {
                for (j = 0; j < filter.options.length; j++) {
                    if (filter.options[j].id == value) {
                        filtersInfo.push([filter.type, filter.options[j].id]);
                        break;
                    }
                }
            } else {
                for (j = 0; j < filter.options.length; j++) {
                    if (filter.options[j].selected) {
                        filtersInfo.push([filter.type, filter.options[j].id]);
                        break;
                    }
                }
            }
        }
        return filtersInfo;
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
    var data;

    this.id = null;
    this.title = null;
    this.price = null;
    this.code = null;
    this.image = null;
    this.originalName = null;
    this.URL = null;

    var iconsInfo;

    this.getId = function() {
        return self.id;
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
        return self.price;
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
        self.id = data.id;
        self.title = data.title;
        self.price = data.price;
        self.code = data.code;
        self.image = data.image;
        self.URL = data.url;
        self.originalName = data.originalName;
        iconsInfo = data.iconsInfo;
    };
    this.getIconsInfo = function() {
        return iconsInfo;
    };
};