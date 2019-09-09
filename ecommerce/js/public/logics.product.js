window.productLogics = new function() {
    var self = this;
    var productsIndex = {};
    var productsLists = [];
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
            var productsListComponent = new ProductsListComponent(elements[i]);
            productsListComponents.push(productsListComponent);
        }
        elements = document.querySelectorAll('.product_details');
        for (i = 0; i < elements.length; i++) {
            new ProductDetailsComponent(elements[i]);
        }
    };
    var importProductsLists = function(data) {
        for (var i = 0; i < data.length; i++) {
            var productsList = new ProductsList(self);
            productsList.importData(data[i]);
            productsLists.push(productsList);
        }
    };
    this.getProduct = function(id) {
        if (typeof productsIndex[id] !== 'undefined') {
            return productsIndex[id];
        }
        return false;
    };
    var trackImpressions = function(productsLists) {
        for (var i = 0; i < productsLists.length; i++) {
            tracking.impressionTracking(productsLists[i].products, productsLists[i].title);
        }
    };
    var receiveData = function(responseStatus, requestName, responseData, callBack) {
        if (responseStatus === 'success' && responseData) {
            callBack(responseData);
        } else {
            controller.fireEvent('ajaxSearchResultsFailure', responseData);
        }
    };
    this.sendQuery = function(callBack, reqUrl) {
        var request = new JsonRequest(reqUrl,
            function(responseStatus, requestName, responseData) {
                return receiveData(responseStatus, requestName, responseData, callBack);
            }, 'ajaxProductsList');

        request.send();
    };
    this.importProduct = function(product) {
        self.productsIndex[product.getId()] = product;
    };

    controller.addListener('initLogics', initLogics);
    controller.addListener('initDom', initComponents);
};

window.ProductsList = function() {
    var self = this;
    this.title = '';
    this.products = [];
    this.productsIndex = {};
    this.importData = function(data) {
        self.id = data.id;

        if (typeof data.title != 'undefined') {
            self.title = data.title;
        }
        if (typeof data.products != 'undefined') {
            for (var i = 0; i < data.products.length; i++) {
                var product = new Product();
                product.importData(data.products[i]);
                self.products.push(product);
                self.productsIndex[product.getId()] = product;

                window.productLogics.importProduct(product);
            }
        }
    };
};
window.Product = function() {
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
    };
};