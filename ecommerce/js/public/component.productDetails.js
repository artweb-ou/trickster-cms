window.ProductDetailsComponent = function(componentElement) {
	var productData;
	var productId;
	var basketButton;
	var amountMinusElement;
	var amountPlusElement;
	var amountInput;
	var productSelectors = [];
	var minimumOrder;
	var priceElements = [];
	var oldPriceElements = [];
	var inquiryLink;
	var inquiryForm;
	var optionsSelected = true;
	var selectedOptions = [];
	var selectedOptionsText = [];
	var selections = [];
	var lastChangedSelection = null;
	var gallery = null;
	var self = this;

	var options;
	var init = function() {
		if (window.productDetailsData) {
			productData = window.productDetailsData;
		} else {
			return false;
		}

		var i;
		productId = parseInt(componentElement.className.split('productid_')[1], 10);

		minimumOrder = 1;
		var minimumOrderElement = _('.product_minimumorder_value', componentElement)[0];
		if (minimumOrderElement) {
			minimumOrder = parseInt(minimumOrderElement.innerHTML, 10);
		}
		if (basketButton = _('.product_details_button', componentElement)[0]) {
			new BasketButtonComponent(basketButton, basketButtonClickHandler);
		}
		if (amountMinusElement = _('.product_details_amount_minus', componentElement)[0]) {
			eventsManager.addHandler(amountMinusElement, 'click', minusClickHandler);
		}
		if (amountPlusElement = _('.product_details_amount_plus', componentElement)[0]) {
			eventsManager.addHandler(amountPlusElement, 'click', plusClickHandler);
		}
		if (amountInput = _('.product_details_amount_input', componentElement)[0]) {
			eventsManager.addHandler(amountInput, 'change', amountChangeHandler);
		}

		if (inquiryLink = _('.product_details_inquiry_link', componentElement)[0]) {
			eventsManager.addHandler(inquiryLink, 'click', inquiryLinkHandler);
			domHelper.addClass(inquiryLink, 'toggleable_component_trigger');
		}
		if (inquiryForm = _('.product_details_inquiry_form', componentElement)[0]) {

			domHelper.addClass(inquiryForm, 'toggleable_component_content');
			var feedback_block = _('.feedback_block', inquiryForm)[0];
			var defaultBehaviour = 'hidden';
			if (domHelper.hasClass(feedback_block, 'feedback_submitted')) {
				defaultBehaviour = 'shown';
			} else {
				domHelper.addClass(inquiryForm, 'toggleable_component_content_hidden');
			}

			options = {
				contentElement: inquiryForm,
				markerElement: inquiryLink,
				afterOpenCallback: inquiryFormOpened,
				defaultBehaviour: defaultBehaviour
			};
			new ToggleableContainer(componentElement, options);
		}
		priceElements = componentElement.querySelectorAll('.product_details_price_digits');
		oldPriceElements = componentElement.querySelectorAll('.product_details_oldprice_digits');
		var selectionElements = _('.product_details_option_control', componentElement);
		for (i = selectionElements.length; i--;) {
			var selection = new ProductDetailsSelectionComponent(self, selectionElements[i]);
			selections.push(selection);
		}
		if (selectionElements.length === 0) {
			// deprecated since 18.10.16
			productSelectors = _('select.product_details_option_selector', componentElement);
			productSelectorsText = _('.product_details_option_text', componentElement);
		}
		if (window.productParametersHintsInfo) {
			var parameterElements = _('.product_details_parameter', componentElement);
			for (i = parameterElements.length; i--;) {
				new ProductDetailsParameterComponent(parameterElements[i]);
			}
		}
		if (typeof window.applicationName !== 'undefined' && window.applicationName === 'mobile') {
			controller.addListener('shoppingBasketProductAdded', shoppingBasketProductAddedHandler);
		}
		var product = getProduct();
		tracking.detailTracking(product);
		gallery = galleriesLogics.getGalleryInfo(productId);
		refresh();
	};
	var inquiryFormOpened = function() {
		TweenLite.to(window, 1, {scrollTo: {y: inquiryForm.offsetTop}, ease: Power2.easeOut});
	};
	var inquiryLinkHandler = function(event) {
		if (_('.product_details_inquiry_form', componentElement)[0]) {
			event.preventDefault();
		}
	};
	var basketButtonClickHandler = function() {
		if (!optionsSelected) {
			// alert(window.translationsLogics.get('product.details_must_select_options'));
			controller.fireEvent('shoppingBasketProductAddFailure', 'product.details_must_select_options');
			return;
		}
		var optionsArgument = selectedOptions;
		var optionsText = selectedOptionsText;
		if (productSelectors.length > 0) {
			// deprecated since 18.10.16
			var selections = [];
			var selectedVariants = [];
			for (var i = 0; i < productSelectors.length; i++) {
				if (productSelectors[i].tagName.toLowerCase() === 'select') {
					selections.push(productSelectors[i].value);
				}
			}
			optionsArgument = selections.join(', ');
		}
		var amount = amountInput ? amountInput.value : minimumOrder;
		if (amount % minimumOrder != 0) {
			amount = minimumOrder;
		}
		shoppingBasketLogics.addProduct(productId, amount, optionsArgument);
	};

	var plusClickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		var amount = parseInt(amountInput.value, 10);
		amount = amount + minimumOrder;
		amountInput.value = amount;
	};
	var minusClickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		var amount = parseInt(amountInput.value, 10);
		amount = amount - minimumOrder;

		if (amount < 1) {
			amount = minimumOrder;
		}
		amountInput.value = amount;
	};
	var amountChangeHandler = function() {
		var amount = parseInt(amountInput.value, 10);
		if (isNaN(amount) || (amount % minimumOrder != 0)) {
			amount = minimumOrder;
		}
		if (amountInput.value != amount) {
			amountInput.value = amount;
		}
	};
	var shoppingBasketProductAddedHandler = function() {
		TweenLite.to(window, 1, {scrollTo: {y: 0, autoKill: false}});
	};
	var refresh = function() {
		var i;
		optionsSelected = true;
		selectedOptions = [];
		selectedOptionsText = [];
		if (!productData) {
			return;
		}
		var influentialOptions = [];
		for (i = selections.length; i--;) {
			var value = selections[i].getValue();
			var text = selections[i].getPlaceholder();
			if (!value) {
				optionsSelected = false;
			} else {
				if (selections[i].isInfluential()) {
					influentialOptions.push(value);
				}
				selectedOptions.push(value);
				selectedOptionsText.push(text);
			}
		}
		if (influentialOptions.length > 0) {
			influentialOptions.sort(function(a, b) {
				return a - b;
			});
			var comboCode = influentialOptions.join(';') + ';';

			if (priceElements) {
				var price = productData.price;
				if (productData.selectionsPricings[comboCode]) {
					price = productData.selectionsPricings[comboCode];
				}
				for (i = priceElements.length; i--;) {
					priceElements[i].innerHTML = price;
				}
			}
			if (oldPriceElements) {
				var oldPrice = productData.oldPrice;
				if (productData.selectionsOldPricings[comboCode]) {
					oldPrice = productData.selectionsOldPricings[comboCode];
				}
				for (i = oldPriceElements.length; i--;) {
					oldPriceElements[i].innerHTML = oldPrice;
				}
			}
		}
	};
	var updateGallery = function() {
		if (gallery && lastChangedSelection) {
			var value = lastChangedSelection.getValue();
			var optionImage = productData.selectionsImages[value];
			if (optionImage) {
				gallery.stopSlideShow();
				gallery.displayImage(optionImage);
			}
		}
	};
	this.selectionChanged = function(selection) {
		lastChangedSelection = selection;
		refresh();
		updateGallery();
	};

	var getProduct = function() {
		var quantity = null;
		if (amountInput) {
			quantity = amountInput.value;
		}
		var price = null;
		if (priceElements && priceElements[0]) {
			if(priceElements[0]) {
				price = priceElements[0].innerText;
			}
		}
		return {
			'id': productId,
			'name': productData.name_ga,
			'category': productData.category_ga,
			'variant': selectedOptionsText,
			'price': price,
			'quantity': quantity
		}
	};
	init();
};

window.ProductDetailsSelectionComponent = function(detailsComponent, componentElement) {
	var id = '';
	var influential = '';
	var selectElement;
	var radioElements;
	var self = this;

	var init = function() {
		id = componentElement.getAttribute('data-elementid');
		influential = !!parseInt(componentElement.getAttribute('data-influential'));
		selectElement = _('select.product_details_option_selector', componentElement)[0];
		if (selectElement) {
			eventsManager.addHandler(selectElement, 'change', change);
		} else {
			radioElements = _('.product_details_option_radio_item_control', componentElement);
			eventsManager.addHandler(componentElement, 'click', change);
		}
		if (window.productParametersHintsInfo && window.productParametersHintsInfo[id]) {
			var hintElement = _('.product_details_option_hint', componentElement.parentElement)[0];
			if(hintElement) {
				var hints = window.productParametersHintsInfo[id];
				var hintContent = hints.join('<hr/>');
				new ToolTipComponent({
					'referralElement' : hintElement,
					'popupText' : hintContent
				});
				hintElement.classList.add('product_details_option_hint_show');
			}
		}
	};
	var change = function(event) {
		detailsComponent.selectionChanged(self);
	};
	this.isInfluential = function() {
		return influential;
	};
	this.getId = function() {
		return id;
	};
	this.getValue = function() {
		if (selectElement) {
			return selectElement.value;
		} else if (radioElements) {
			for (var i = radioElements.length; i--;) {
				if (radioElements[i].checked) {
					return radioElements[i].value;
				}
			}
		}
		return false;
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	this.getPlaceholder = function() {
		if (selectElement) {
			return selectElement.value;
		} else if (radioElements) {
			for (var i = radioElements.length; i--;) {
				if (radioElements[i].checked) {
					return radioElements[i].placeholder;
				}
			}
		}
		return false;
	};

	init();
};

window.ProductDetailsParameterComponent = function(componentElement) {
	var hintElement;
	var hints = [];

	var init = function() {
		var id = componentElement.className.slice(componentElement.className.indexOf('product_details_parameter_id_') + 29);
		if (id.indexOf(' ') > 0) {
			id = id.slice(0, id.indexOf(' '));
		}
		hintElement = _('.product_details_parameter_hinttrigger', componentElement)[0];
		if (window.productParametersHintsInfo[id] && hintElement) {
			hints = window.productParametersHintsInfo[id];
			var hintContent = hints.join('<hr/>');
			new ToolTipComponent(hintElement, hintContent, false, 'product_details_parameter_tooltip');
		}
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	init();
};