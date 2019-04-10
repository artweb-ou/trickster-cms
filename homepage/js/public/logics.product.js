window.productLogics = new function() {
	var productsIndex = {};

	var init = function() {
		var impressionProducts = Array();
		if (typeof window.products !== 'undefined' && window.products) {
			for (var i = 0; i < window.products.length; i++) {
				var products = new Product(window.products[i]);
				productsIndex[products.getId()] = products;
				impressionProducts.push(products);
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

window.Product = function(data) {
	var product = data;

	this.getId = function() {
		if(product.id){
			return product.id;
		}
		return false;

	};

	this.getName = function() {
		if(product.name_ga){
			return product.name_ga;
		}
		return false;
	};

	this.getBrand = function() {
		if(product.brand_ga){
			return product.brand_ga;
		}
		return false;
	};

	this.getCategory = function() {
		if(product.category_ga){
			return product.category_ga;
		}
		return false;
	};

	this.getVariant = function() {
		if(product.variant){
			return product.variant;
		}
		return false;
	};

	this.getPrice = function() {
		if(product.price){
			return product.price;
		}
		return false;
	};

	this.getQuantity = function() {
		if(product.quantity){
			return product.quantity;
		}
		return false;
	};

	this.getCoupon = function() {
		if(product.coupon){
			return product.coupon;
		}
		return false;
	};

	this.getPosition = function() {
		if(product.position){
			return product.position;
		}
		return false;
	};
};