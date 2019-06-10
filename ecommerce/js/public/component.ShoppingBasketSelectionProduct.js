window.ShoppingBasketSelectionProduct = function(basketProductId, initData) {
    var self = this;

    var productData = false;
    var changeTimeOut = false;
    var keyUpDelay = 400;
    var amountUpDelay = 200;
    var minimumOrder = 1;

    var amountPlusButton;
    var amountMinusButton;
    var amountInput;
    var removeButton;

    var componentElement;

    var init = function() {
        productData = initData;
    };
    this.setComponentElement = function(newComponentElement) {
        componentElement = newComponentElement;
        if (amountMinusButton = componentElement.querySelector('.shoppingbasket_table_amount_minus')) {
            eventsManager.addHandler(amountMinusButton, 'click', minusClickHandler);
        }
        if (amountInput = componentElement.querySelector('.shoppingbasket_table_amount_input')) {
            eventsManager.addHandler(amountInput, 'keyup', amountKeyUpHandler);
            eventsManager.addHandler(amountInput, 'change', amountChangeHandler);
            new window.InputComponent({'componentClass': 'shoppingbasket_table_amount_block', 'inputElement': amountInput});
        }
        if (amountPlusButton = componentElement.querySelector('.shoppingbasket_table_amount_plus')) {
            eventsManager.addHandler(amountPlusButton, 'click', plusClickHandler);
        }
        if (removeButton = componentElement.querySelector('.shoppingbasket_table_remove')) {
            eventsManager.addHandler(removeButton, 'click', removeClickHandler);
        }
    };

    var plusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInput.value, 10);
        amount = amount + minimumOrder;
        amountInput.value = amount;

        window.clearTimeout(changeTimeOut);
        changeTimeOut = window.setTimeout(changeAmount, amountUpDelay);
    };
    var minusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInput.value, 10);
        amount = amount - minimumOrder;

        if (amount < minimumOrder) {
            amount = minimumOrder;
        }

        amountInput.value = amount;

        window.clearTimeout(changeTimeOut);
        changeTimeOut = window.setTimeout(changeAmount, amountUpDelay);
    };
    var amountKeyUpHandler = function() {
        window.clearTimeout(changeTimeOut);
        changeTimeOut = window.setTimeout(changeAmount, keyUpDelay);
    };
    var changeAmount = function() {
        var amount = parseInt(amountInput.value, 10);
        if (amount % minimumOrder != 0) {
            amount = minimumOrder;
        }
        if (!isNaN(amount) && amount > 0) {
            registerEventHandlers();
            window.shoppingBasketLogics.changeAmount(basketProductId, amount);
        }
    };

    var shoppingBasketProductAdditionHandler = function() {
        unRegisterEventHandlers();
    };
    var shoppingBasketProductAddFailureHandler = function() {
        unRegisterEventHandlers();
        if (addToBasketButtonAction) {
            var message = [];
            var additionalContainerClassName = 'notice_box';
            message['title'] = window.productDetailsData.name || window.productDetailsData.name_ga;
            message['content'] = window.translationsLogics.get('product.quantityunavailable');
            message['footer'] = '';
            // only modal on error
            new ModalActionComponent(false, false, componentElement, additionalContainerClassName, '', message); // checkbox-input, footer buttons, element for position, messages
        }

        amountInput.value--;
    };

    var registerEventHandlers = function() {
        controller.addListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler);
        controller.addListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler);
    };

    var unRegisterEventHandlers = function() {
        controller.removeListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler);
        controller.removeListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler);
    };

    var amountChangeHandler = function() {
        window.clearTimeout(changeTimeOut);

        var amount = parseInt(amountInput.value, 10);
        if (isNaN(amount) || amount < 1) {
            amount = 1;
        }
        if (amountInput.value != amount) {
            amountInput.value = amount;
        }
        changeAmount();
    };

    var removeClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        window.shoppingBasketLogics.removeProduct(basketProductId);
    };

    this.getUrl = function() {
        return productData.url;
    };
    this.getTitle = function() {
        return productData.title;
    };
    this.getCode = function() {
        return productData.code;
    };
    this.getBasketProductId = function() {
        return basketProductId;
    };
    this.getImage = function() {
        return productData.image;
    };
    this.getAmount = function() {
        return productData.amount;
    };
    this.getPrice = function() {
        return productData.price;
    };
    this.getSalesPrice = function() {
        return productData.salesPrice;
    };
    this.getTotalPrice = function() {
        return productData.totalPrice;
    };
    this.getCategory = function() {
        return productData.category;
    };
    this.getVariationsText = function() {
        var text = '';
        var variations = [];
        if (productData.variation) {
            if (typeof productData.variation == 'object' && productData.variation.length) {
                variations = productData.variation;
            } else if (typeof productData.variation == 'string') {
                variations.push(productData.variation);
            }
        }
        if (variations.length) {
            var variationHtml = [];
            variations.forEach(function(variation, i) {
                var variationsArray = variation.split(':');
                variationHtml[i] = '<span class="variation_name">' + variationsArray[0] + '</span><span class="variation_separator"></span><span class="variation_value">' + variationsArray[1] + '</span>';
            });
            variations = variationHtml;
            text = '<p>' + variations.join('</p><p>') + '</p>';
        }
        return text;
    };
    init();
};
DomElementMakerMixin.call(ShoppingBasketSelectionProduct.prototype);
