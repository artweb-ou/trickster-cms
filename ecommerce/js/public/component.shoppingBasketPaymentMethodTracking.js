window.ShoppingBasketPaymentMethodTracking = new function() {
	var paymentMethodsContainer;
	var chosenPaymentMethod;
	var paymentMethod = '';
	var lastText = ' ';

	var init = function() {
		paymentMethodsContainer = document.querySelectorAll('.shopping_basket_selection_paymentmethods_options');
		var inputEl;
		if (paymentMethodsContainer) {
			for (var i = 0; i < paymentMethodsContainer.length; i++) {
				chosenPaymentMethod = paymentMethodsContainer[i].querySelector('span.radiobutton');
				inputEl = paymentMethodsContainer[i].querySelector('.shoppingbasket_paymentmethod_radio');
				if(inputEl && inputEl.checked && inputEl.dataset.paymentTitle) {
					paymentMethod = inputEl.dataset.paymentTitle;
				}
				eventsManager.addHandler(chosenPaymentMethod, 'click', clickHandler);

			}
			paymentMethodTracking();
		}

	};
	var paymentMethodTracking = function() {
		if (window.jsonData && window.jsonData.shoppingBasketData) {
			if (paymentMethod != lastText) {
				tracking.checkoutOptionsTracking(window.jsonData.shoppingBasketData.currentStep, paymentMethod);
				lastText = paymentMethod;
			}
		}
	}
	var clickHandler = function(e) {
		paymentMethod = e.target.parentElement.title;
		inputEl = e.target.parentElement.querySelector('.shoppingbasket_paymentmethod_radio');
		if(inputEl&& inputEl.dataset.paymentTitle) {
			paymentMethod = inputEl.dataset.paymentTitle;
			paymentMethodTracking();
		}
	};

	init();
	controller.addListener('startApplication', init);
};