window.ShoppingBasketPaymentMethodTracking = new function() {
    var paymentMethodsContainer;
    var chosenPaymentMethod;
    var lastText = ' ';

    var init = function() {
        paymentMethodsContainer = document.querySelector('.shopping_basket_selection_paymentmethods_options');
        if (paymentMethodsContainer) {
            if (window.jsonData && window.jsonData.shoppingBasketData) {
                tracking.checkoutProgressTracking(window.jsonData.shoppingBasketData.productsList);
            }
            chosenPaymentMethod = paymentMethodsContainer.querySelectorAll('.shoppingbasket_paymentmethod');
            for (var i = 0; i < chosenPaymentMethod.length; i++) {
                eventsManager.addHandler(chosenPaymentMethod[i], 'click', clickHandler);
            }
        }

    };

    var clickHandler = function(e) {
        var paymentMethod = e.target.parentElement.title;
        if (paymentMethod && lastText !== paymentMethod) {
            tracking.checkoutOptionsTracking(2, paymentMethod);
        }
        lastText = e.target.parentElement.title;
    };

    init();

    controller.addListener('initDom', init);
};