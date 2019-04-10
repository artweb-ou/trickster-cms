window.ShoppingBasketStatusComponent = function(componentElement) {
	var self = this;
	var emptyTextElement;
	var amountElement;
	var priceElement;
	var priceValueElement;
	var currencyElement;
	var popupComponent;
	var clonedStatusElement;
	var positions;
	var floatingActivated = false;
	var topOffset = 0;
	var init = function() {
		emptyTextElement = _('.shoppingbasket_status_empty', componentElement)[0];
		amountElement = _('.shoppingbasket_status_amount', componentElement)[0];
		if (priceElement = _('.shoppingbasket_status_price', componentElement)[0]) {
			priceValueElement = _('.shoppingbasket_status_price_value', priceElement)[0];
			currencyElement = _('.shoppingbasket_status_currency', priceElement)[0];
		}
		var basketPopupElement = _('.shoppingbasket_popup')[0];
		if (basketPopupElement) {
			popupComponent = new ShoppingBasketPopupComponent(basketPopupElement, self);
			popupComponent.updatePositions();
			eventsManager.addHandler(componentElement, 'click', click);
		}
		controller.addListener('startApplication', updateData);
		controller.addListener('shoppingBasketUpdated', updateData);

		if (domHelper.hasClass(componentElement, 'shoppingbasket_status_floating')) {
			window.eventsManager.addHandler(window, 'scroll', scrollHandler);
			window.eventsManager.addHandler(window, 'resize', scrollHandler);
			floatingActivated = true;

			controller.addListener('floatingHeaderMoved', floatingHeaderMovedHandler);
		}
	};

	var scrollHandler = function() {
		var floatable = false;
		if (window.shoppingBasketLogics.productsList.length > 0) {
			if (floatingActivated && !clonedStatusElement && !isScrolledIntoView(componentElement)) {
				clonedStatusElement = componentElement.cloneNode(true);
				domHelper.addClass(clonedStatusElement, 'shoppingbasket_status_hidden_clone');
				clonedStatusElement.style.visibility = 'hidden';
				componentElement.parentNode.insertBefore(clonedStatusElement, componentElement);
			}

			if (!isScrolledIntoView(clonedStatusElement)) {
				floatable = true;
			}
			if (floatable && clonedStatusElement) {
				domHelper.addClass(componentElement, 'shoppingbasket_status_floating_active');
				var top = topOffset;

				positions = domHelper.getElementPositions(clonedStatusElement);
				componentElement.style.position = 'fixed';
				componentElement.style.left = positions.x + 'px';
				componentElement.style.right = 'auto';
				componentElement.style.top = top + 'px';
			} else {
				resetFloating();
			}
		}
		if (popupComponent) {
			popupComponent.updatePositions();
		}
	};

	var resetFloating = function() {
		componentElement.style.position = '';
		componentElement.style.left = '';
		componentElement.style.right = '';
		componentElement.style.top = '';

		domHelper.removeClass(componentElement, 'shoppingbasket_status_floating_active');
	};

	var isScrolledIntoView = function(element) {
		if (element) {
			var elementTop = element.getBoundingClientRect().top;
			var elementBottom = element.getBoundingClientRect().bottom;
			return elementTop >= 0 && elementBottom <= window.innerHeight;
		}
	};

	var click = function(event) {
		if (window.shoppingBasketLogics.productsList.length > 0) {
			eventsManager.preventDefaultAction(event);
			popupComponent.display();
		}
	};

	var updateData = function() {
		var text;
		var productsAmount = window.shoppingBasketLogics.productsAmount;
		if (productsAmount > 0) {
			if (emptyTextElement) {
				emptyTextElement.style.display = 'none';
			}
			if (priceElement) {
				var productsPrice = window.shoppingBasketLogics.productsSalesPrice;
				text = domHelper.roundNumber(productsPrice, 2);
				priceValueElement.innerHTML = text;
				priceElement.style.display = 'block';
			}
			if (amountElement) {
				text = window.translationsLogics.get('shoppingbasket.status_amount').replace('%s', productsAmount);
				amountElement.innerHTML = text;
				amountElement.style.display = 'block';
			}
			if (currencyElement) {
				currencyElement.innerHTML = window.selectedCurrencyItem.symbol;
			}
			if (floatingActivated) {
				scrollHandler();
			}

		} else {
			if (emptyTextElement) {
				text = window.translationsLogics.get('shoppingbasket.status_empty');
				emptyTextElement.innerHTML = text;
				emptyTextElement.style.display = 'block';
			}
			if (priceElement) {
				priceElement.style.display = 'none';
			}
			if (amountElement) {
				amountElement.style.display = 'none';
			}
			resetFloating();

			if (popupComponent) {
				popupComponent.updatePositions();
			}
		}

	};
	this.getComponentElement = function() {
		return componentElement;
	};

	var floatingHeaderMovedHandler = function(headerInfo) {
		topOffset = headerInfo.currentHeight;
		if (floatingActivated) {
			componentElement.style.top = topOffset + 'px';
			if (popupComponent) {
				popupComponent.updatePositions();
			}
		}
	};
	init();
};