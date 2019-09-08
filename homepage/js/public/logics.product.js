window.productLogics = new function() {
    var productsIndex = {};
    var productsList = [];
    var initLogics = function() {
        if (typeof window.products !== 'undefined') {
            importData(window.products);
        }
        trackImpressions(productsList);
    };
    var initComponents = function() {
        var elements, i;
        elements = document.querySelectorAll('.productslist_products');
        for (i = 0; i < elements.length; i++) {
            new ProductShortComponent(elements[i]);
        }
    };
    var importData = function(data) {
        for (let i = 0; i < data.length; i++) {
            var product = new Product();
            product.importData(data[i]);
            productsList.push(product);
            productsIndex[product.getId()] = product;
        }
    };
    this.getProduct = function(id) {
        if (typeof productsIndex[id] !== 'undefined') {
            return productsIndex[id];
        }
        return false;
    };
    var trackImpressions = function(products) {
        tracking.impressionTracking(products);
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

    controller.addListener('initLogics', initLogics);
    controller.addListener('initDom', initComponents);
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