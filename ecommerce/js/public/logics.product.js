window.productLogics = new function() {
    var self = this;
    var productsIndex = {};

    var productSearchFormsIndex = {};
    var productSearchComponents = {};

    var productsListsIndex = {};
    var productsListComponents = [];

    var initLogics = function() {
        if (typeof window.productsLists !== 'undefined') {
            importProductsLists(window.productsLists);
        }
        if (typeof window.productSearchForms !== 'undefined') {
            importProductSearchForms(window.productSearchForms);
        }
        trackImpressions(productsListComponents);
    };

    var initComponents = function() {
        var elements, i, id;
        elements = document.querySelectorAll('.productslist_component');
        for (i = 0; i < elements.length; i++) {
            id = elements[i].dataset.id;
            if (typeof productsListsIndex[id] !== 'undefined') {
                var productsListComponent = new ProductsListComponent(elements[i]);
                productsListComponents.push(productsListComponent);
            }
        }
        elements = document.querySelectorAll('.productsearch');
        for (i = 0; i < elements.length; i++) {
            id = elements[i].dataset.id;
            if (typeof productsListsIndex[id] !== 'undefined') {
                var productSearch = new ProductSearchComponent(elements[i]);
                productSearchComponents.push(productSearch);
            }

            new ProductSearchComponent(elements[i]);
        }
        elements = document.querySelectorAll('.product_details');
        for (i = 0; i < elements.length; i++) {
            new ProductDetailsComponent(elements[i]);
        }

        elements = _('.selectedproducts_content_scrolltype');
        for (i = elements.length; i--;) {
            new SelectedProductsScrollComponent(elements[i]);
        }
        elements = _('.selectedproducts_column');
        for (i = elements.length; i--;) {
            new SelectedProductsColumnComponent(elements[i]);
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

    var importProductSearchForms = function(data) {
        var productSearchForm;
        for (var i = 0; i < data.length; i++) {
            var id = data[i].id;
            if (typeof productSearchFormsIndex[id] === 'undefined') {
                productSearchForm = new ProductSearch(self);
                productSearchFormsIndex[id] = productSearchForm;
            } else {
                productSearchForm = productSearchFormsIndex[id];
            }
            productSearchForm.importData(data[i]);
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

    this.getProductSearchForm = function(id) {
        if (typeof productSearchFormsIndex[id] !== 'undefined') {
            return productSearchFormsIndex[id];
        }
        return false;
    };

    var trackImpressions = function(productsLists) {
        for (var i = 0; i < productsLists.length; i++) {
            tracking.impressionTracking(productsLists[i].getCurrentPageProducts(), productsLists[i].title);
        }
    };

    var receiveData = function(responseStatus, requestName, responseData) {
        if (requestName === 'ajaxProductsList') {
            if (responseStatus === 'success' && responseData) {
                if (typeof responseData.productsList !== 'undefined') {
                    importProductsLists([responseData.productsList]);
                }
                if (typeof responseData.productSearch !== 'undefined') {
                    importProductSearchForms(responseData.productSearch);
                }
            } else {
                controller.fireEvent('ajaxSearchResultsFailure', responseData);
            }
        }
    };

    this.requestProductsListData = function(productsListId, page, filters, sorting, limit) {
        var reqUrl = '/ajaxProductsList/';
        var parameters = {
            'listElementId': productsListId,
            'page': page,
        };
        if (typeof filters !== 'undefined') {
            for (let i in filters) {
                if (filters.hasOwnProperty(i)) {
                    parameters[i] = filters[i].join(',');
                }
            }
        }
        if (typeof sorting !== 'undefined') {
            parameters['sort'] = sorting;
        }
        if (typeof limit !== 'undefined') {
            parameters['limit'] = limit;
        }
        var request = new JsonRequest(reqUrl, receiveData, 'ajaxProductsList', parameters);
        request.send();
    };

    this.getMainProductsList = function() {
        for (var i in productsListsIndex) {
            return productsListsIndex[i];
        }
        return false;
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
    this.filterOrder = null;
    this.filterSort = null;
    this.filterLimit = 0;
    this.filterDiscountIds = [];
    this.filterBrandIds = [];
    this.filterCategoryIds = [];
    this.filterActiveParametersInfo = false;
    this.filterAvailability = [];
    this.filterPrice = [];
    this.currentPage = 1;
    this.productsLayout = 'thumbnail';

    var filters = [];
    var productsByPages = {};
    var productsIndex = {};

    this.importData = function(data) {
        var i;
        self.id = data.id;
        self.url = data.url;
        self.filterOrder = data.filterOrder;
        self.filterSort = data.filterSort;
        self.filterLimit = data.filterLimit;
        if (data.filteredProductsAmount) {
            self.filteredProductsAmount = data.filteredProductsAmount;
        } else {
            self.filteredProductsAmount = 0;
        }

        if (data.filterDiscountIds) {
            self.filterDiscountIds = data.filterDiscountIds;
        } else {
            self.filterDiscountIds = [];
        }
        if (data.filterBrandIds) {
            self.filterBrandIds = data.filterBrandIds;
        } else {
            self.filterBrandIds = [];
        }
        if (data.filterCategoryIds) {
            self.filterCategoryIds = data.filterCategoryIds;
        } else {
            self.filterCategoryIds = [];
        }
        if (data.filterActiveParametersInfo) {
            self.filterActiveParametersInfo = data.filterActiveParametersInfo;
        } else {
            self.filterActiveParametersInfo = false;
        }
        if (data.filterAvailability) {
            self.filterAvailability = data.filterAvailability;
        } else {
            self.filterAvailability = [];
        }
        if (data.filterPrice) {
            self.filterPrice = data.filterPrice;
        } else {
            self.filterPrice = [];
        }
        self.currentPage = data.currentPage;
        self.productsLayout = data.productsLayout;

        if (typeof data.title != 'undefined') {
            self.title = data.title;
        }
        if (typeof data.products != 'undefined') {
            productsByPages[self.currentPage] = [];
            for (i = 0; i < data.products.length; i++) {
                var product = new Product();
                product.importData(data.products[i]);

                productsByPages[self.currentPage].push(product);
                productsIndex[product.getId()] = product;
                window.productLogics.importProduct(product);
            }
        } else {
            productsByPages[self.currentPage] = [];
        }
        if (typeof data.filters != 'undefined') {
            filters = [];
            for (i = 0; i < data.filters.length; i++) {
                var filter = new ProductsListFilter();
                filter.importData(data.filters[i]);
                filters.push(filter);
            }
        }
        controller.fireEvent('productsListUpdated', self.id);
        tracking.impressionTracking(self.getCurrentPageProducts(), self.title);
    };
    this.changePage = function(newPageNumber) {
        if (self.currentPage !== newPageNumber) {
            var sorting = generateSortingString();
            let filtersInfo = getFiltersInfo();
            productLogics.requestProductsListData(self.id, newPageNumber, filtersInfo, sorting, self.filterLimit);
        }
    };
    const getFiltersInfo = function(id, value) {
        let filtersInfo = {};

        if (id === 'price') {
            filtersInfo[id] = value;
        } else if (self.filterPrice.length) {
            filtersInfo['price'] = self.filterPrice;
        }
        if (id === 'discount') {
            filtersInfo[id] = value;
        } else if (self.filterDiscountIds.length) {
            filtersInfo['discount'] = self.filterDiscountIds;
        }
        if (id === 'brand') {
            filtersInfo[id] = value;
        } else if (self.filterBrandIds.length) {
            filtersInfo['brand'] = self.filterBrandIds;
        }
        if (id === 'category') {
            filtersInfo[id] = value;
        } else if (self.filterCategoryIds.length) {
            filtersInfo['category'] = self.filterCategoryIds;
        }
        if (id === 'availability') {
            filtersInfo[id] = value;
        } else if (self.filterAvailability.length) {
            filtersInfo['availability'] = self.filterAvailability;
        }

        if (!isNaN(id)) {
            filtersInfo['parameter'] = value;
        } else {
            filtersInfo['parameter'] = [];
        }
        if (self.filterActiveParametersInfo) {
            for (let i in self.filterActiveParametersInfo) {
                if (self.filterActiveParametersInfo.hasOwnProperty(i)) {
                    if (i != id) {
                        filtersInfo['parameter'] = filtersInfo['parameter'].concat(self.filterActiveParametersInfo[i]);
                    }
                }
            }
        }

        return filtersInfo;
    };
    this.changeFilter = function(filtersId, filterValues) {
        let sorting = generateSortingString();
        let filtersInfo = getFiltersInfo(filtersId, filterValues);
        productLogics.requestProductsListData(self.id, 0, filtersInfo, sorting, self.filterLimit);
    };
    this.changeSorting = function(sorting) {
        let filtersInfo = getFiltersInfo();
        productLogics.requestProductsListData(self.id, 0, filtersInfo, sorting, self.filterLimit);
    };
    this.changeLimit = function(limit) {
        var sorting = generateSortingString();
        let filtersInfo = getFiltersInfo();
        productLogics.requestProductsListData(self.id, 0, filtersInfo, sorting, limit);
    };
    var generateSortingString = function() {
        return self.filterSort + ';' + self.filterOrder;
    };
    this.getCurrentPageProducts = function() {
        if (typeof productsByPages[self.currentPage] !== 'undefined') {
            return productsByPages[self.currentPage];
        }
        return false;
    };
    this.getFilters = function() {
        return filters;
    };

    this.reset = function() {
        productLogics.requestProductsListData(self.id, 0);
    };
    // var generateQueryString = function(arguments) {
    //     var queryString = '';
    //     for (var key in arguments) {
    //         queryString += key + ':' + arguments[key].join(',') + '/';
    //     }
    //     // workaround for retaining order
    //     var currentUrl = document.location.href;
    //     var sortArgumentPosition = currentUrl.indexOf('sort:');
    //     if (sortArgumentPosition > 0) {
    //         var sortSlice = currentUrl.slice(sortArgumentPosition);
    //         if (sortSlice.indexOf('limit:') <= 0) {
    //             var limitArgumentPosition = currentUrl.indexOf('limit:');
    //             if (limitArgumentPosition > 0) {
    //                 sortSlice = currentUrl.slice(limitArgumentPosition);
    //             }
    //         }
    //         queryString += sortSlice;
    //     }
    //     return encodeURI(queryString);
    // };

};

window.ProductSearch = function() {
    var self = this;
    this.id = null;
    this.title = null;
    this.url = null;
    this.checkboxesForParameters = false;
    this.pricePresets = false;

    var filters = [];

    this.importData = function(data) {
        var i;
        self.id = data.id;
        self.url = data.url;

        if (typeof data.checkboxesForParameters != 'undefined') {
            self.checkboxesForParameters = data.checkboxesForParameters;
        }
        if (typeof data.pricePresets != 'undefined') {
            self.pricePresets = data.pricePresets;
        }
        if (typeof data.title != 'undefined') {
            self.title = data.title;
        }
        if (typeof data.filters != 'undefined') {
            filters = [];
            for (i = 0; i < data.filters.length; i++) {
                var filter = new ProductsListFilter();
                filter.importData(data.filters[i]);
                filters.push(filter);
            }
        }
        controller.fireEvent('productsSearchFormUpdated', self.id);
    };
    this.getFilters = function() {
        return filters;
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

    this.isEmptyPrice = function() {
        return (self.price === '');
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

window.ProductsListFilter = function() {
    var id;
    var title;
    var type;
    var optionsInfo;

    var range;
    var selectedRange;

    this.getId = function() {
        return id;
    };

    this.getTitle = function() {
        return title;
    };

    this.getType = function() {
        return type;
    };

    this.getOptionsInfo = function() {
        return optionsInfo;
    };

    this.getRange = function() {
        return range;
    };

    this.getSelectedRange = function() {
        return selectedRange;
    };

    this.importData = function(newData) {
        id = newData.id;
        title = newData.title;
        type = newData.type;
        optionsInfo = newData.options;
        selectedRange = newData.selectedRange;
        range = newData.range;

        optionsInfo = newData.options;
    };

    this.getValue = function() {
        let value = [];
        for (let i = 0; i < optionsInfo.length; i++) {
            if (optionsInfo[i].selected) {
                value.push(optionsInfo[i].id);
            }
        }
        return value;
    };
};