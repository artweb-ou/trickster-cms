window.productLogics = new function() {
    var productsIndex = {};

    var init = function() {
        var impressionProducts = Array();
        if (typeof window.products !== 'undefined' && window.products) {
            for (var i = 0; i < window.products.length; i++) {
                var product = new Product();
                product.updateData(window.products[i]);
                productsIndex[product.getId()] = product;
                impressionProducts.push(product);
            }
            tracking.impressionTracking(impressionProducts);
        }
    };

    this.getProduct = function(id) {
        if (typeof productsIndex[id] !== 'undefined') {
            return productsIndex[id];
        }
        return false;
    };

    controller.addListener('initDom', init);
};

window.Product = function() {
    var self = this;
    var legacyApproachData;
    this.title = null;
    this.id = null;

    this.setData = function(newData) {
        //self.id = newData.id;
        //self.title = newData.title;

        legacyApproachData = newData;
    };

    this.getId = function() {
        if (legacyApproachData.id) {
            return legacyApproachData.id;
        }
        return false;

    };

    this.getName = function() {
        if (legacyApproachData.name_ga) {
            return legacyApproachData.name_ga;
        }
        return false;
    };

    this.getBrand = function() {
        if (legacyApproachData.brand_ga) {
            return legacyApproachData.brand_ga;
        }
        return false;
    };

    this.getCategory = function() {
        if (legacyApproachData.category_ga) {
            return legacyApproachData.category_ga;
        }
        return false;
    };

    this.getVariant = function() {
        if (legacyApproachData.variant) {
            return legacyApproachData.variant;
        }
        return false;
    };

    this.getPrice = function() {
        if (legacyApproachData.price) {
            return legacyApproachData.price;
        }
        return false;
    };

    this.getQuantity = function() {
        if (legacyApproachData.quantity) {
            return legacyApproachData.quantity;
        }
        return false;
    };

    this.getCoupon = function() {
        if (legacyApproachData.coupon) {
            return legacyApproachData.coupon;
        }
        return false;
    };

    this.getPosition = function() {
        if (legacyApproachData.position) {
            return legacyApproachData.position;
        }
        return false;
    };
};