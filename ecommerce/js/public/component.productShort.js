function ProductShortComponent(componentElement) {
    var productId;
    var basketButton;
    var detailsButton;
    var linkElement;
    var amountMinusElement;
    var amountPlusElement;
    var amountInputElement;
    var optionSelectElements;
    var minimumOrder;

    var init = function() {
        productId = parseInt(componentElement.className.split('productid_')[1], 10);

        var controlsElement = componentElement.querySelector('.product_short_controls');
        if (controlsElement) {
            var value = controlsElement.dataset.minimumOrder;
            if (value) {
                minimumOrder = parseInt(value, 10);
            } else {
                minimumOrder = 1;
            }

            if (amountInputElement = controlsElement.querySelector('.product_short_amount_input')) {
                eventsManager.addHandler(amountInputElement, 'change', amountChangeHandler);
                if (amountMinusElement = controlsElement.querySelector('.product_short_amount_button_minus')) {
                    eventsManager.addHandler(amountMinusElement, 'click', minusClickHandler);
                }
                if (amountPlusElement = controlsElement.querySelector('.product_short_amount_button_plus')) {
                    eventsManager.addHandler(amountPlusElement, 'click', plusClickHandler);
                }
            }
        }
        if (basketButton = componentElement.querySelector('.product_short_basket')) {
            new BasketButtonComponent(basketButton, onBasketButtonClick, productId);
        }
        if (detailsButton = componentElement.querySelector('.product_short_details')) {
            eventsManager.addHandler(detailsButton, 'click', clickHandler);
        }
        if (linkElement = componentElement.querySelector('.product_short_link')) {
            eventsManager.addHandler(componentElement, 'click', clickHandler);
        }
        optionSelectElements = componentElement.querySelector('.select.product_short_option_select');
    };

    var amountChangeHandler = function() {
        var amount = parseInt(amountInputElement.value, 10);
        if (amount !== amount) {
            amount = 1;
        }
        if (amountInputElement.value != amount) {
            amountInputElement.value = amount;
        }
    };

    var plusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInputElement.value, 10);
        amount = amount + minimumOrder;
        amountInputElement.value = amount;
    };

    var minusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInputElement.value, 10);
        amount = amount - minimumOrder;

        if (amount < 1) {
            amount = minimumOrder;
        }
        amountInputElement.value = amount;
    };

    var onBasketButtonClick = function(event) {
        var amount = amountInputElement ? amountInputElement.value : minimumOrder;
        if (amount % minimumOrder != 0) {
            amount = minimumOrder;
        }
        var variation = '';
        if (optionSelectElements) {
            for (var i = 0; i < optionSelectElements.length; ++i) {
                if (i != 0) {
                    variation += ', ';
                }
                variation += optionSelectElements[i].value;
            }
        }
        shoppingBasketLogics.addProduct(productId, amount, variation);
    };

    var clickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        eventsManager.cancelBubbling(event);
        tracking.productClickTracking(productId, trackingEndCallback);
    };
    var trackingEndCallback = function() {
        document.location.href = linkElement.href;
    };

    init();
}