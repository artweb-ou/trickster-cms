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
        controller.addListener('shoppingBasketProductChangeFailure', shoppingBasketProductChangeFailure);
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
            window.shoppingBasketLogics.changeAmount(basketProductId, amount);
        }
    };

    var shoppingBasketProductChangeFailure = function() {
        var message = [];
        var additionalContainerClassName = 'notice_box';
        message['title'] = productData.title;
        message['content'] = window.translationsLogics.get('product.quantityunavailable');
        message['footer'] = '';
        new ModalActionComponent(false, false, componentElement, additionalContainerClassName, '', message);

        amountInput.value = productData.amount;
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
                variationHtml[i] = '<div class="variation_name variation_separator">' + variationsArray[0] + '</div><div class="variation_value">' + variationsArray[1] + '</div>';
            });
            variations = variationHtml;
            text = '<div class="variation_container">' + variations.join('</div><div class="variation_container">') + '</div>';
        }
        return text;
    };
    init();
};
DomElementMakerMixin.call(ShoppingBasketSelectionProduct.prototype);
