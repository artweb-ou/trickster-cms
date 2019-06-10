window.shoppingBasketLogics = new function() {
    var self = this;

    var elementId;

    this.selectedDeliveryTypeId = 0;
    this.selectedCountryId = 0;
    this.selectedCityId = 0;
    this.promoCodeDiscountId = 0;

    this.productsAmount = 0;
    this.productsPrice = 0;
    this.totalPrice = 0;
    this.vatAmount = 0;
    this.deliveryPrice = 0;
    this.message = '';
    this.amount = 0;

    this.productsList = [];
    this.productsIndex = {};

    this.countriesList = [];
    this.countriesIndex = {};

    this.deliveryTypesList = [];
    this.deliveryTypesIndex = {};
    this.selectedDeliveryTypeTitle = '';

    this.discountsList = [];
    this.discountsIndex = {};

    this.showInBasketDiscountsList = [];

    this.servicesList = [];
    this.servicesIndex = {};
    var selectedServicesPrice;

    this.productsSalesPrice = 0;

    this.displayVat = true;
    this.displayTotals = true;
    var orderId;
    var paymentStatus = false;
    var initData = function() {
        if (window.jsonData && window.jsonData.shoppingBasketData) {
            importData(window.jsonData.shoppingBasketData);
        }
        if (window.orders != undefined) {
            paymentStatus = window.orders[0].orderStatus;
        }
        self.trackingPurchase();
    };

    var importData = function(basketData) {
        orderId = parseInt(basketData.orderId, 10);
        self.displayVat = basketData.displayVat;
        self.displayTotals = basketData.displayTotals;
        self.selectedCountryId = parseInt(basketData.selectedCountryId, 10);
        self.selectedCityId = parseInt(basketData.selectedCityId, 10);
        self.selectedDeliveryTypeId = parseInt(basketData.selectedDeliveryTypeId, 10);
        self.selectedDeliveryTypeTitle = basketData.selectedDeliveryTypeTitleDl;
        self.promoCodeDiscountId = parseInt(basketData.promoCodeDiscountId, 10);
        self.productsAmount = parseInt(basketData.productsAmount, 10);
        if (basketData.deliveryPrice !== '') {
            self.deliveryPrice = basketData.deliveryPrice;
        } else {
            self.deliveryPrice = '';
        }
        self.productsPrice = basketData.productsPrice;
        self.totalPrice = basketData.totalPrice;
        self.vatAmount = basketData.vatAmount;
        self.vatLessTotalPrice = basketData.vatLessTotalPrice;

        self.productsSalesPrice = basketData.productsSalesPrice;

        self.message = basketData.message;
        selectedServicesPrice = basketData.selectedServicesPrice;

        elementId = parseInt(basketData.elementId, 10);

        var usedIdIndex = {};
        for (var i = 0; i < basketData.productsList.length; i++) {
            var basketProductId = basketData.productsList[i].basketProductId;

            usedIdIndex[basketProductId] = true;

            var product = false;
            if (!self.productsIndex[basketProductId]) {
                product = new ShoppingBasketProduct();
                self.productsIndex[basketProductId] = product;
                self.productsList.push(product);
            } else {
                product = self.productsIndex[basketProductId];
            }

            if (product) {
                product.updateData(basketData.productsList[i]);
            }
        }

        for (var i = 0; i < self.productsList.length; i++) {
            var basketProductId = self.productsList[i].basketProductId;
            if (typeof usedIdIndex[basketProductId] == 'undefined') {
                delete self.productsIndex[basketProductId];
                self.productsList.splice(i, 1);
            }
        }

        for (var i = 0; i < basketData.countriesList.length; i++) {
            var countryId = basketData.countriesList[i].id;

            var country = false;
            if (!self.countriesIndex[countryId]) {
                country = new ShoppingBasketCountry();
                self.countriesIndex[countryId] = country;
                self.countriesList.push(country);
            } else {
                country = self.countriesIndex[countryId];
            }

            if (country) {
                country.updateData(basketData.countriesList[i]);
            }
        }

        self.deliveryTypesList = [];
        self.deliveryTypesIndex = {};

        for (var i = 0; i < basketData.deliveryTypesList.length; i++) {
            var deliveryId = basketData.deliveryTypesList[i].id;

            var delivery = new ShoppingBasketDelivery();
            self.deliveryTypesIndex[deliveryId] = delivery;
            self.deliveryTypesList.push(delivery);

            delivery.updateData(basketData.deliveryTypesList[i]);
        }

        self.discountsList = [];
        self.discountsIndex = {};

        for (var i = 0; i < basketData.discountsList.length; i++) {
            var discountId = basketData.discountsList[i].id;

            var discount = new ShoppingBasketDiscount();
            self.discountsIndex[discountId] = discount;
            self.discountsList.push(discount);

            discount.updateData(basketData.discountsList[i]);
        }

        self.showInBasketDiscountsList = [];
        for (var i = 0; i < basketData.showInBasketDiscountsList.length; i++) {
            var discountId = basketData.showInBasketDiscountsList[i].id;

            var discount = new ShowInBasketDiscount();
            self.showInBasketDiscountsList.push(discount);

            discount.updateData(basketData.showInBasketDiscountsList[i]);
        }

        self.servicesList = [];
        self.servicesIndex = {};

        for (var i = 0; i < basketData.servicesList.length; i++) {
            var serviceId = basketData.servicesList[i].id;

            var service = new ShoppingBasketService();
            self.servicesIndex[serviceId] = service;
            self.servicesList.push(service);

            service.updateData(basketData.servicesList[i]);
        }
        controller.fireEvent('shoppingBasketUpdated');
    };

    var initComponents = function() {
        var elements = document.querySelectorAll('.shoppingbasket_status');
        for (var i = 0; i < elements.length; i++) {
            new ShoppingBasketStatusComponent(elements[i]);
        }
        var elements = _('.shoppingbasket_selection');
        for (var i = 0; i < elements.length; i++) {
            new ShoppingBasketSelectionComponent(elements[i]);
        }
    };
    this.trackingPurchase = function() {
        if ((paymentStatus || paymentStatus == 'undefined') && orderId) {
            tracking.buyTracking(orderId);
        }
    };
    this.trackCheckout = function() {
        if (!paymentStatus && self.productsList) {
            tracking.checkoutTracking(self.productsList);
            tracking.checkoutOptionsTracking(1, self.selectedDeliveryTypeTitle);
        }
    };
    this.addProduct = function(productId, productAmount, options) {
        if (typeof productAmount == 'undefined') {
            productAmount = 1;
        }
        self.amount = productAmount;
        var requestParameters = [];
        requestParameters['productId'] = productId;
        requestParameters['productAmount'] = productAmount;
        if (typeof options == 'string') {
            // deprecated since 18.10.16
            requestParameters['productVariation'] = options;
        } else if (options) {
            requestParameters['productOptions'] = options;
        }
        sendData('addProduct', requestParameters);

        if (typeof fbq != 'undefined') {
            fbq('track', 'AddToCart');
        }

        if (typeof fbq != 'undefined') {
            fbq('track', 'AddToCart');
        }
    };
    this.changeAmount = function(basketProductId, productAmount) {
        if (typeof productAmount == 'undefined') {
            productAmount = 1;
        }

        var requestParameters = [];
        requestParameters['basketProductId'] = basketProductId;
        requestParameters['productAmount'] = productAmount;

        sendData('changeAmount', requestParameters);
    };

    this.setPromoCode = function(promoCode) {
        var requestParameters = [];
        requestParameters['promoCode'] = promoCode;

        sendData('setPromoCode', requestParameters);
    };

    this.changeProductAmount = function(basketProductId, productAmount) {
        var parameters = [];
        parameters['basketProductId'] = basketProductId;
        parameters['productAmount'] = amount;

        sendData('changeAmount', parameters);
    };

    var getProduct = function(id) {
        if (self.productsIndex[id]) {
            return self.productsIndex[id];
        }
        return false;
    };

    this.removeProduct = function(basketProductId) {
        var parameters = [];
        parameters['basketProductId'] = basketProductId;
        var product;
        if (product = getProduct(basketProductId)) {
            tracking.removeFromBasket(product);
        }

        sendData('removeProduct', parameters);
    };
    this.selectDelivery = function(deliveryId) {
        var parameters = [];
        parameters['deliveryId'] = deliveryId;

        sendData('selectDelivery', parameters);
    };
    this.selectDeliveryCountry = function(deliveryCountryId) {
        var parameters = [];
        parameters['deliveryCountryId'] = deliveryCountryId;

        sendData('selectDeliveryCountry', parameters);
    };
    this.selectDeliveryCity = function(deliveryTargetId) {
        var parameters = [];
        parameters['deliveryTargetId'] = deliveryTargetId;

        sendData('selectDeliveryTarget', parameters);
    };
    this.setServiceSelection = function(serviceId, selected) {
        var parameters = [];
        parameters['serviceId'] = serviceId;
        parameters['selected'] = selected;

        sendData('changeService', parameters);
    };
    this.getSelectedCountry = function() {
        var result = false;
        if (self.countriesIndex[self.selectedCountryId]) {
            result = self.countriesIndex[self.selectedCountryId];
        }
        return result;
    };
    this.getSelectedDeliveryType = function() {
        var result = false;
        if (self.deliveryTypesIndex[self.selectedDeliveryTypeId]) {
            result = self.deliveryTypesIndex[self.selectedDeliveryTypeId];
        }
        return result;
    };
    this.getPromoCodeDiscount = function() {
        var result = false;
        if (self.discountsIndex[self.promoCodeDiscountId]) {
            result = self.discountsIndex[self.promoCodeDiscountId];
        }
        return result;
    };
    this.getSelectedCity = function() {
        var result = false;
        if (self.countriesIndex[self.selectedCountryId]) {
            if (self.countriesIndex[self.selectedCountryId].citiesIndex[self.selectedCityId]) {
                result = self.countriesIndex[self.selectedCountryId].citiesIndex[self.selectedCityId];
            }
        }
        return result;
    };
    this.getCitiesList = function() {
        var result = false;
        if (self.countriesIndex[self.selectedCountryId]) {
            result = self.countriesIndex[self.selectedCountryId].citiesList;
        }
        return result;
    };
    this.getDiscountsList = function() {
        return self.discountsList;
    };
    this.getShowInBasketDiscountsList = function() {
        return self.showInBasketDiscountsList;
    };
    this.getServicesList = function() {
        return self.servicesList;
    };
    this.getSelectedServices = function() {
        var selectedServices = [];
        for (var i = 0; i < self.servicesList.length; ++i) {
            if (self.servicesList[i].selected) {
                selectedServices[selectedServices.length] = self.servicesList[i];
            }
        }
        return selectedServices;
    };
    this.getSelectedServicesPrice = function() {
        return selectedServicesPrice;
    };
    var sendData = function(actionName, requestParameters) {
        var requestURL = window.ajaxURL + 'id:' + elementId + '/action:' + actionName;
        var request = new JsonRequest(requestURL, receiveData, actionName, requestParameters);
        request.send();
    };

    var receiveData = function(responseStatus, requestName, parsedData) {
        if (responseStatus == 'success') {
            if (typeof parsedData.shoppingBasketData != 'undefined') {
                importData(parsedData.shoppingBasketData);
                if (requestName == 'selectDelivery') {
                    tracking.checkoutOptionsTracking(1, parsedData.shoppingBasketData.selectedDeliveryTypeTitleDl);
                }
            }
            if (requestName == 'addProduct') {
                controller.fireEvent('shoppingBasketProductAdded');
                var products = parsedData.shoppingBasketData.productsList;
                var lastAddedProduct = parsedData.shoppingBasketData.productsList[products.length - 1];
                tracking.addToBasketTracking(lastAddedProduct, self.amount);
            }

        } else if (responseStatus == 'fail') {
            if (requestName == 'addProduct' || requestName == 'changeAmount') {
                controller.fireEvent('shoppingBasketProductAddFailure', 'product.quantityunavailable');
            }
        }
    };
    controller.addListener('initLogics', initData);
    controller.addListener('initDom', initComponents);
};

window.ShoppingBasketProduct = function() {
    this.updateData = function(data) {
        importData(data);
        // recalculate();
    };
    var importData = function(data) {
        self.basketProductId = data.basketProductId;
        self.productId = parseInt(data.productId, 10);
        self.price = data.price;
        self.emptyPrice = data.emptyPrice;
        self.unit = data.unit;
        self.salesPrice = data.salesPrice;
        self.totalSalesPrice = data.totalSalesPrice;
        self.totalPrice = data.totalPrice;
        self.amount = parseInt(data.amount, 10);
        self.minimumOrder = parseInt(data.minimumOrder, 10);
        if (self.minimumOrder < 1) {
            self.minimumOrder = 1;
        }
        self.title = data.title;
        self.title_dl = data.title_dl;
        self.category = data.category;
        self.description = data.description;
        self.variation = data.variation;
        self.variation_dl = '';
        for (var i = 0; i < data.variation_dl.length; i++) {
            self.variation_dl += data.variation_dl[i] + ' ';
        }
        self.code = data.code;
        self.image = data.image;
        self.url = data.url;
    };
    var recalculate = function() {
        self.totalPrice = self.price * self.amount;
    };
    var self = this;

    this.basketProductId = false;
    this.productId = 0;
    this.price = 0;
    this.emptyPrice = 0;
    this.unit = '';
    this.salesPrice = 0;
    this.totalSalesPrice = 0;
    this.totalPrice = 0;
    this.amount = 0;
    this.minimumOrder = 0;
    this.title = '';
    this.description = '';
    this.variation = '';
    this.code = '';
    this.image = '';
    this.url = '';
};
window.ShoppingBasketCountry = function() {
    this.updateData = function(data) {
        importData(data);
    };
    var importData = function(data) {
        self.id = parseInt(data.id, 10);
        self.title = data.title;
        if (data.iso3166_1a2 != undefined) {
            self.iso3166_1a2 = data.iso3166_1a2;
        }
        self.conditionsText = data.conditionsText;

        self.citiesIndex = {};
        self.citiesList = [];
        for (var i = 0; i < data.citiesList.length; i++) {
            var city = new shoppingBasketCountryCity();
            self.citiesIndex[data.citiesList[i].id] = city;
            self.citiesList.push(city);
            city.updateData(data.citiesList[i]);
        }
    };
    var self = this;

    this.id = false;
    this.title = '';
    this.iso3166_1a2 = '';
    this.conditionsText = '';
    this.citiesList = [];
    this.citiesIndex = {};
};
window.shoppingBasketCountryCity = function() {
    var self = this;

    this.id = false;
    this.title = '';
    this.updateData = function(data) {
        importData(data);
    };
    var importData = function(data) {
        self.id = parseInt(data.id, 10);
        self.title = data.title;
    };
};
window.ShoppingBasketDelivery = function() {
    this.updateData = function(data) {
        importData(data);
    };
    var importData = function(data) {
        self.id = parseInt(data.id, 10);
        self.price = parseFloat(data.price, 10);
        self.title = data.title;
        self.code = data.code;
        self.deliveryFormFields = data.deliveryFormFields;
        self.hasNeededReceiverFields = data.hasNeededReceiverFields;
    };
    var self = this;

    this.id = false;
    this.price = 0;
    this.title = '';
    this.code = '';
    this.deliveryFormFields = [];
};
window.ShoppingBasketDiscount = function() {
    this.updateData = function(data) {
        importData(data);
    };
    var importData = function(data) {
        self.id = parseInt(data.id, 10);
        self.amount = parseFloat(data.amount, 10);
        self.title = data.title;
        self.code = data.code;
    };
    var self = this;

    this.id = false;
    this.price = 0;
    this.title = '';
    this.code = '';
};
window.ShowInBasketDiscount = function() {
    this.updateData = function(data) {
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
    };
    var self = this;

    this.id = false;
    this.title = '';
    this.code = '';
    this.basketText = '';
    this.displayText = false;
    this.displayProductsInBasket = false;
    this.products = [];
};
window.ShoppingBasketService = function() {
    this.updateData = function(data) {
        importData(data);
    };
    var importData = function(data) {
        self.id = parseInt(data.id, 10);
        self.price = parseFloat(data.price, 10);
        self.title = data.title;
        self.selected = data.selected;
    };
    var self = this;

    this.id = false;
    this.price = 0;
    this.title = '';
    this.selected = false;
};