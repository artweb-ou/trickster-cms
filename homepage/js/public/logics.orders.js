window.ordersLogics = new function() {
	var ordersIndex = {};
	var init = function() {
		if (typeof window.orders !== 'undefined') {
			for (var i = 0; i < window.orders.length; i++) {
				var order = new Order(window.orders[i]);
				ordersIndex[order.getId()] = order;
			}
		}
	};

	this.getOrder = function(id) {
		if (typeof ordersIndex[id] !== 'undefined') {
			return ordersIndex[id];
		}
		return false;
	};

	controller.addListener('initLogics', init);
};

window.Order = function(data) {
	var id;
	var currency;
	var vatAmount;
	var products = [];
	var revenue;
	var shippingPrice;
	var coupon = '';
	var invoiceNumber;
	var discounts;
	var self = this;
	var init = function() {
		self.importData(data);
	};

	this.getId = function() {
		return id;
	};

	this.getInvoiceNumber = function() {
		return invoiceNumber;
	};

	this.getCurrency = function() {
		return currency;
	};

	this.getPriceWithoutVat = function() {
		var price = parseFloat(revenue) - parseFloat(vatAmount);
		return price.toFixed(2);
	};

	this.getVat = function() {
		return vatAmount;
	};

	this.getShippingPrice = function() {
		return shippingPrice;
	};

	this.getPromoCode = function() {
		for(var i = 0; i < discounts.length; i++){
			coupon += discounts[i]['title']+' ';
		}
		return coupon;

	};

	this.getProducts = function() {
		return products;
	};

	this.getCoupon = function() {
		var coupon;
		if(discounts) {
			for(var i = 0; i < discounts.length; i++) {
				if(discounts.length === 1){
					coupon = discounts[i].title + ' ('+discounts[i].id+')';
				}
			}
		}
		return coupon;
	};

	this.importData = function() {
		id = data.id;
		invoiceNumber = data.invoiceNumber;
		currency = data.currency;
		vatAmount = data.vatAmount;
		revenue = data.totalPrice;
		shippingPrice = data.deliveryPrice;
		discounts = data.discounts;
		products = data.products;
	};
	init()
};