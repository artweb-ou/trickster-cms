window.productLogics = new function() {
    var productsIndex = {};

    var init = function() {
        var impressionProducts = Array();
        if (typeof window.products !== 'undefined' && window.products) {
            for (var i = 0; i < window.products.length; i++) {
                var products = new Product(window.products[i]);
                productsIndex[products.getId()] = products;
                impressionProducts.push(products);
/*
                var product = new Product();
                product.updateData(window.products[i]);
                productsIndex[product.getId()] = product;
                impressionProducts.push(product);
*/
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

/*    this.updateData = function(data) {
        importData(data);
    };
    var importData = function(data) {
        self.id = parseInt(data.id, 10);
        self.title = data.title;
        self.code = data.code;
        self.basketText = data.basketText;
        self.displayText = data.displayText;
        self.displayProductsInBasket = data.displayProductsInBasket;
        self.products = data.products;
    };*/
};
/*

window.HeaderGalleryImageProduct = function() {
    var self = this;

    var id;
    var positionX;
    var positionY;
    var title;
    var description;
    var price;
    var image;
    var url;
    var primaryParametersInfo;
    var markerLogic;

    this.updateData = function(data) {
        id = parseInt(data.id, 10);
        positionX = parseFloat(data.positionX, 10);
        positionY = parseFloat(data.positionY, 10);
        title = data.title;
        description = data.description;
        price = parseFloat(data.price, 10);
        image = data.image;
        url = data.url;
        primaryParametersInfo = data.primaryParametersInfo;
    };

    this.getId = function() {
        return id;
    };
    this.getTitle = function() {
        return title;
    };

 */
window.Product = function(data) {
 //   var self = this;
    var product = data;

    this.getId = function() {
        if (product.id) {
            return product.id;
        }
        return false;

    };

    this.getName = function() {
        if (product.title_ga) {
            return product.title_ga;
        }
        return false;
    };

    this.getBrand = function() {
        if (product.brand_ga) {
            return product.brand_ga;
        }
        return false;
    };

    this.getCategory = function() {
        if (product.category_ga) {
            return product.category_ga;
        }
        return false;
    };

    this.getVariant = function() {
        if (product.variant) {
            return product.variant;
        }
        return false;
    };

    this.getPrice = function() {
        if (product.price) {
            return product.price;
        }
        return false;
    };

    this.getQuantity = function() {
        if (product.quantity) {
            return product.quantity;
        }
        return false;
    };

    this.getCoupon = function() {
        if (product.coupon) {
            return product.coupon;
        }
        return false;
    };

    this.getPosition = function() {
        if (product.position) {
            return product.position;
        }
        return false;
    };
};

/*
window.Product = function() {
    var self = this;
    var legacyApproachData;
    var updateData;
    this.title = null;
    this.id = null;

    this.setData = function(newData) {
        //self.id = newData.id;
        //self.title = newData.title;

        legacyApproachData = newData;
        var self = this;
        // var product = data;
        var product = newData;
    };

    this.getId = function() {
        if (this.id) {
            return self.id;
        }
        return false;
    };

    this.getName = function() {
        if (product.title_ga) {
            return this.title_ga;
        }
        return false;
    };

    this.getBrand = function() {
        if (this.brand_ga) {
            return this.brand_ga;
        }
        return false;
    };

    this.getCategory = function() {
        if (this.category_ga) {
            return this.category_ga;
        }
        return false;
    };

    this.getVariant = function() {
        if (this.variant) {
            return this.variant;
        }
        return false;
    };

    this.getPrice = function() {
        if (this.price) {
            return this.price;
        }
        return false;
    };

    this.getQuantity = function() {
        if (this.quantity) {
            return this.quantity;
        }
        return false;
    };

    this.getCoupon = function() {
        if (this.coupon) {
            return this.coupon;
        }
        return false;
    };

    this.getPosition = function() {
        if (this.position) {
            return this.position;
        }
        return false;
    };
};*/
