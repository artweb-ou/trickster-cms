window.ProductShortComponent = function(componentElement) {
    let productId;
    let basketButton;
    let detailsButton;
    let linkElement;
    let amountMinusElement;
    let amountPlusElement;
    let amountInputElement;
    let optionSelectElements;
    let minimumOrder;

    let init = function() {
        productId = parseInt(componentElement.className.split('productid_')[1], 10);

        let controlsElement = componentElement.querySelector('.product_short_controls');
        if (controlsElement) {
            let value = controlsElement.dataset.minimumOrder;
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

    let amountChangeHandler = function() {
        let amount = parseInt(amountInputElement.value, 10);
        if (amount !== amount) {
            amount = 1;
        }
        if (amountInputElement.value != amount) {
            amountInputElement.value = amount;
        }
    };

    let plusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        let amount = parseInt(amountInputElement.value, 10);
        amount = amount + minimumOrder;
        amountInputElement.value = amount;
    };

    let minusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        let amount = parseInt(amountInputElement.value, 10);
        amount = amount - minimumOrder;

        if (amount < 1) {
            amount = minimumOrder;
        }
        amountInputElement.value = amount;
    };

    let onBasketButtonClick = function() {
        let amount = amountInputElement ? amountInputElement.value : minimumOrder;
        if (amount % minimumOrder != 0) {
            amount = minimumOrder;
        }
        shoppingBasketLogics.addProduct(productId, amount);
    };

    let clickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        eventsManager.cancelBubbling(event);
        tracking.productClickTracking(productId, trackingEndCallback);
    };
    let trackingEndCallback = function() {
        document.location.href = linkElement.href;
    };

    init();
}