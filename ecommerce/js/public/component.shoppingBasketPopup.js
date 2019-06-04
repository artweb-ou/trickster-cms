window.ShoppingBasketPopupComponent = function(componentElement, parentObject) {
	var productsElement, totalElement, totalContainerElement;
	var productsIndex = {};
	var visible = false;
	var self = this;

	var init = function() {
		document.body.appendChild(componentElement);
		var closerElement = _('.shoppingbasket_popup_closer', componentElement)[0];
		productsElement = _('.shoppingbasket_popup_products', componentElement)[0];
		totalContainerElement = _('.shoppingbasket_popup_total', componentElement)[0];
		totalElement = _('.shoppingbasket_popup_total_value', componentElement)[0];
		if (closerElement) {
			eventsManager.addHandler(closerElement, 'click', closerClick);
		}
		refresh();
		controller.addListener('shoppingBasketUpdated', shoppingBasketUpdated);
		eventsManager.addHandler(componentElement, 'click', click);
	};
	var closerClick = function(event) {
		self.hide();
	};

	var click = function(event) {
		eventsManager.cancelBubbling(event);
	};
	var shoppingBasketUpdated = function() {
		refresh();
	};
	var refresh = function() {
		var productsInfo = window.shoppingBasketLogics.productsList;
		if (productsInfo.length) {
			var usedIdIndex = {};
			for (var i = 0; i < productsInfo.length; ++i) {
				var productInfo = productsInfo[i];
				usedIdIndex[productInfo.basketProductId] = true;

				if (typeof productsIndex[productInfo.basketProductId] == 'undefined') {
					var product = new ShoppingBasketPopupProductComponent(productsInfo[i]);
					productsElement.appendChild(product.getComponentElement());
					productsIndex[productInfo.basketProductId] = product;
				} else {
					productsIndex[productInfo.basketProductId].updateContents(productInfo);
				}
			}
			for (var id in productsIndex) {
				if (typeof usedIdIndex[id] == 'undefined') {
					productsElement.removeChild(productsIndex[id].getComponentElement());
					delete productsIndex[id];
				}
			}
			var totalPrice = window.shoppingBasketLogics.productsSalesPrice;
			totalElement.innerHTML = totalPrice + ' ' + window.selectedCurrencyItem.symbol;

			if (window.shoppingBasketLogics.displayTotals) {
				totalContainerElement.style.display = 'block';
			} else {
				totalContainerElement.style.display = 'none';
			}

		} else if (visible) {
			self.hide();
		}
	};
	var bodyClick = function() {
		self.hide();
	};
	this.display = function() {
		if (visible) {
			return;
		}
		componentElement.style.visibility = 'hidden';
		componentElement.style.display = 'block';
		self.updatePositions();
		componentElement.style.visibility = 'visible';
		visible = true;
		window.setTimeout(function() {
			// let the event that triggered display bubble to body before adding listener
			eventsManager.addHandler(document.body, 'click', bodyClick);
		}, 0);
	};
	this.updatePositions = function() {
		var statusElement = parentObject.getComponentElement();
		var positions = self.getPosition(statusElement);
		componentElement.style.left = (positions.x + statusElement.offsetWidth - componentElement.offsetWidth) + "px";
		componentElement.style.top = (positions.y + statusElement.offsetHeight) + "px";
	};
	this.hide = function() {
		if (!visible) {
			return;
		}
		visible = false;
		componentElement.style.display = 'none';
		eventsManager.removeHandler(document.body, 'click', bodyClick);
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	this.isVisible = function() {
		return visible;
	};
	init();
};
DomHelperMixin.call(ShoppingBasketPopupComponent.prototype);

window.ShoppingBasketPopupProductComponent = function(productInfo) {

	var componentElement, imageElement, priceElement, titleElement, amountInputElement, variationElement,
		salesPriceElement, fullPriceElement;
	var changeTimeout;
	var keyUpDelay = 400;
	var minimumOrder = 1;
	var self = this;

	var init = function() {
		componentElement = self.makeElement('div', 'shoppingbasket_popup_product');
		var innerElement = self.makeElement('div', 'shoppingbasket_popup_product_inner', componentElement);
		imageElement = self.makeElement('div', 'shoppingbasket_popup_product_image_wrap', innerElement).appendChild(self.makeElement('img', 'shoppingbasket_popup_product_image'));
		var detailsElement = self.makeElement('div', 'shoppingbasket_popup_product_details', innerElement);
		priceElement = self.makeElement('div', 'shoppingbasket_popup_product_price', detailsElement);

		salesPriceElement = self.makeElement('span', 'shoppingbasket_popup_product_price_sales', priceElement);
		fullPriceElement = self.makeElement('span', 'shoppingbasket_popup_product_price_full', priceElement);

		titleElement = self.makeElement('a', 'shoppingbasket_popup_product_title', detailsElement);

		variationElement = self.makeElement('p', 'shoppingbasket_popup_product_variation', detailsElement);

		var controlsElement = self.makeElement('div', 'shoppingbasket_popup_product_controls', detailsElement);
		var amountElement = self.makeElement('div', 'shoppingbasket_popup_product_amount', controlsElement);
		amountElement.appendChild(document.createTextNode(window.translationsLogics.get('shoppingbasketpopup.amount') + ':'));
		amountInputElement = self.makeElement('input', 'input_component shoppingbasket_popup_product_amount_input', amountElement);
		var deleteLinkElement = self.makeElement('span', 'shoppingbasket_popup_product_delete', controlsElement);
		deleteLinkElement.innerHTML = window.translationsLogics.get('shoppingbasketpopup.remove');

		self.updateContents(productInfo);
		eventsManager.addHandler(deleteLinkElement, 'click', deleteLinkElementClick);
		eventsManager.addHandler(amountInputElement, 'keyup', amountKeyUpHandler);
		eventsManager.addHandler(amountInputElement, 'change', amountChangeHandler);
	};
	var amountKeyUpHandler = function() {
		window.clearTimeout(changeTimeout);
		changeTimeout = window.setTimeout(changeAmount, keyUpDelay);
	};
	var amountChangeHandler = function() {
		window.clearTimeout(changeTimeout);

		var amount = parseInt(amountInputElement.value, 10);
		if (isNaN(amount) || amount < 1) {
			amount = 1;
		}
		if (amountInputElement.value != amount) {
			amountInputElement.value = amount;
		}
		changeAmount();
	};
	var changeAmount = function() {
		var amount = parseInt(amountInputElement.value, 10);
		if (amount % minimumOrder != 0) {
			amount = minimumOrder;
		}
		if (!isNaN(amount) && amount > 0) {
			registerEventHandlers();
			window.shoppingBasketLogics.changeAmount(productInfo.basketProductId, amount);
		}
	};
	var deleteLinkElementClick = function(event) {
		eventsManager.preventDefaultAction(event);
		window.shoppingBasketLogics.removeProduct(productInfo.basketProductId);
	};
	var shoppingBasketProductAdditionHandler = function() {
		unRegisterEventHandlers();
	};
	var shoppingBasketProductAddFailureHandler = function() {
		unRegisterEventHandlers();
		alert(window.translationsLogics.get('product.quantityunavailable'));
		amountInputElement.value--;
	};
	var registerEventHandlers = function() {
		controller.addListener("shoppingBasketProductAdded", shoppingBasketProductAdditionHandler);
		controller.addListener("shoppingBasketProductAddFailure", shoppingBasketProductAddFailureHandler);
	};
	var unRegisterEventHandlers = function() {
		controller.removeListener("shoppingBasketProductAdded", shoppingBasketProductAdditionHandler);
		controller.removeListener("shoppingBasketProductAddFailure", shoppingBasketProductAddFailureHandler);
	};
	this.updateContents = function(newData) {
		if (newData.image != '') {
			imageElement.src = newData.image;
			imageElement.parentNode.style.display = 'block';
		} else {
			imageElement.parentNode.style.display = 'none';
		}
		titleElement.innerHTML = newData.title;
		titleElement.href = newData.url;
		var variations = [];
		if (newData.variation) {
			if (typeof newData.variation == 'object' && newData.variation.length) {
				variations = newData.variation;
			} else if (typeof newData.variation == 'string') {
				variations.push(newData.variation);
			}
		}
		variationElement.innerHTML = variations.join(', ');

		if (!newData.emptyPrice) {
			salesPriceElement.innerHTML = newData.totalSalesPrice + ' ' + window.selectedCurrencyItem.symbol;
		}
		if (newData.totalSalesPrice != newData.totalPrice) {
			fullPriceElement.innerHTML = newData.totalPrice + ' ' + window.selectedCurrencyItem.symbol;
		} else {
			fullPriceElement.innerHTML = '';
		}

		amountInputElement.value = newData.amount;
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	init();
};
DomElementMakerMixin.call(ShoppingBasketPopupProductComponent.prototype);
