window.ShoppingBasketSelectionComponent = function(componentElement) {
	var conditionsContentElement;
	var conditionsTextElement;
	var submitButtonElement;
	var formElement;
	var contentsElement;
	var messageElement;
	var productPriceElements;
	var totalsContainerElement;
	var footerTotalsContainerElement;
	var totalsComponent;
	var showInBasketDiscountsComponent;
	var conditionsCheckboxInput;

	var init = function() {
		contentsElement = _('.shoppingbasket_contents', componentElement)[0]
		if (contentsElement) {
			conditionsContentElement = _('.shoppingbasket_form_conditions_content', formElement)[0]
			conditionsTextElement = _('.shoppingbasket_form_conditions_text', conditionsContentElement)[0]
		}
		if (formElement = _('.shoppingbasket_form', componentElement)[0]) {
			if (submitButtonElement = _('.shoppingbasket_form_submit')[0]) {
				eventsManager.addHandler(submitButtonElement, 'click', submitForm)
			}
		}

		messageElement = _('.shoppingbasket_selection_message', componentElement)[0]
		var element = _('.shoppingbasket_table', componentElement)[0]
		if (element) {
			new ShoppingBasketSelectionTable(element)
		}
		totalsContainerElement = _('.shoppingbasket_total_products')[0]
		productPriceElements = _('.shoppingbasket_total_products .shoppingbasket_total_value')
		element = _('.shoppingbasket_services_component', componentElement)[0]
		if (element) {
			new ShoppingBasketSelectionServices(element)
		}
		element = _('.shoppingbasket_form_block', componentElement)[0]
		if (element) {
			new ShoppingBasketSelectionForm(element, formElement)
		}

		element = _('.shoppingbasket_promocode', componentElement)[0]
		if (element) {
			new ShoppingBasketPromoCodeComponent(element)
		}

		footerTotalsContainerElement = _('.shoppingbasket_totals_table', componentElement)[0]

		if (footerTotalsContainerElement) {
			var element2 = _('.shoppingbasket_totals', componentElement)[0]
			if (element2) {
				totalsComponent = new ShoppingBasketTotalsComponent(element2)
			}
		}

		conditionsCheckboxInput = componentElement.querySelector('#shoppingbasket_form_conditions_checkbox');

		showInBasketDiscountsComponent = _('.shoppingbasket_discounts', componentElement)[0];

		controller.addListener('startApplication', startApplication);
		controller.addListener('shoppingBasketUpdated', updateData);
	};
	var startApplication = function() {
		window.shoppingBasketLogics.trackCheckout();
		updateData()
	};
	var updateData = function() {
		if (messageElement) {
			messageElement.innerHTML = window.shoppingBasketLogics.message
		}
		if (conditionsContentElement && conditionsTextElement) {
			var selectedCountry = shoppingBasketLogics.getSelectedCountry()
			if (selectedCountry && selectedCountry.conditionsText) {
				conditionsContentElement.style.display = 'block'
				conditionsTextElement.innerHTML = selectedCountry.conditionsText
			} else {
				conditionsContentElement.style.display = ''
			}
		}
		var products = window.shoppingBasketLogics.productsList
		if (products.length > 0) {
			for (var i = 0; i < productPriceElements.length; i++) {
				productPriceElements[i].innerHTML = domHelper.roundNumber(shoppingBasketLogics.productsSalesPrice, 2) + ' ' + window.selectedCurrencyItem.symbol
			}
			if (totalsComponent) {
				totalsComponent.updateData()
			}
			contentsElement.style.display = 'block'
		} else {
			contentsElement.style.display = 'none'
		}

		if (window.shoppingBasketLogics.displayTotals) {
			if (footerTotalsContainerElement) {
				footerTotalsContainerElement.style.display = '';
			}

			if (totalsContainerElement) {
				totalsContainerElement.style.display = '';
			}
		} else {
			if (footerTotalsContainerElement) {
				footerTotalsContainerElement.style.display = 'none';
			}

			if (totalsContainerElement) {
				totalsContainerElement.style.display = 'none';
			}
		}

		if (showInBasketDiscountsComponent) {
			showInBasketDiscountsComponent.innerHTML = ''
			var showInBasketDiscountsList = window.shoppingBasketLogics.getShowInBasketDiscountsList()
			for (var i = 0; i < showInBasketDiscountsList.length; i++) {
				var showInBasketDiscountElement = document.createElement('div')
				showInBasketDiscountElement.className = 'shoppingbasket_discount'
				showInBasketDiscountsComponent.appendChild(showInBasketDiscountElement)

				if (showInBasketDiscountsList[i].displayText) {
					var discountTextElement = document.createElement('div')
					discountTextElement.className = 'shoppingbasket_discount_text'
					discountTextElement.innerHTML = showInBasketDiscountsList[i].basketText
					showInBasketDiscountElement.appendChild(discountTextElement)
				}

				if (showInBasketDiscountsList[i].displayProductsInBasket) {
					var discountProductsElement = document.createElement('div')
					discountProductsElement.className = 'shoppingbasket_discount_products'
					showInBasketDiscountElement.appendChild(discountProductsElement)
					for (var j = 0; j < showInBasketDiscountsList[i].products.length; j++) {
						var currentProduct = showInBasketDiscountsList[i].products[j]
						var discountProductElement = document.createElement('section')
						discountProductElement.className = 'subcontentmodule_component subcontentmodule_square product_buttonsmall product_short productid_' + currentProduct.id
						discountProductsElement.appendChild(discountProductElement)

						var titleElement = document.createElement('h2')
						titleElement.className = 'subcontentmodule_title product_buttonsmall_title'
						titleElement.innerHTML = currentProduct.title
						discountProductElement.appendChild(titleElement)

						var productContentElement = document.createElement('div')
						productContentElement.className = 'subcontentmodule_content'
						discountProductElement.appendChild(productContentElement)

						var productHrefElement = document.createElement('a')
						productHrefElement.className = 'product_buttonsmall_link'
						productHrefElement.href = currentProduct.URL
						productContentElement.appendChild(productHrefElement)

						if (currentProduct.originalName != '') {
							var productThumbnailElement = document.createElement('div')
							productThumbnailElement.className = 'product_buttonsmall_image_container'
							productHrefElement.appendChild(productThumbnailElement)

							var productImageElement = document.createElement('img')
							productImageElement.className = 'product_buttonsmall_image'
							productImageElement.src = window.baseURL + 'image/type:productSmallThumb/id:' + currentProduct.image + '/filename:' + currentProduct.originalName
							productImageElement.alt = currentProduct.title
							productThumbnailElement.appendChild(productImageElement)

							if (currentProduct.icons || currentProduct.connectedDiscounts) {
								var productIconsElement = document.createElement('div')
								productIconsElement.className = 'product_buttonsmall_icons'
								productThumbnailElement.appendChild(productIconsElement)

								if (currentProduct.oldPrice) {
									var productDiscountCountainerElement = document.createElement('div')
									productDiscountCountainerElement.className = 'product_discount_container'
									productIconsElement.appendChild(productDiscountCountainerElement)

									var productOldPriceElement = document.createElement('span')
									productOldPriceElement.className = 'product_discount'
									productOldPriceElement.innerHTML = '-' + currentProduct.discountPercent + '%'
									productDiscountCountainerElement.appendChild(productOldPriceElement)
								}

								for (var q = 0; q < currentProduct.icons.length; q++) {
									var productIconElement = document.createElement('img')
									productIconElement.className = 'product_buttonsmall_icons_image'
									productIconElement.src = window.baseURL + 'image/type:productIcon/id:' + currentProduct.icons[q].image + '/filename:' + currentProduct.icons[q].originalName
									productIconElement.alt = currentProduct.icons[q].title
									productIconsElement.appendChild(productIconElement)
								}

								for (var q = 0; q < currentProduct.connectedDiscounts.length; q++) {
									if (currentProduct.connectedDiscounts[q].icon) {
										var productDiscountElement = document.createElement('img')
										productDiscountElement.className = 'product_buttonsmall_icons_image discount_icon'
										productDiscountElement.src = window.baseURL + 'image/type:productIcon/id:' + currentProduct.connectedDiscounts[q].icon + '/filename:' + currentProduct.connectedDiscounts[q].originalName
										productDiscountElement.alt = currentProduct.connectedDiscounts[q].title
										productIconsElement.appendChild(productDiscountElement)
									}
								}
							}
						}

						if (currentProduct.price) {
							var productPriceElement = document.createElement('span')
							productPriceElement.className = 'product_buttonsmall_price'
							productPriceElement.innerHTML = currentProduct.price + window.selectedCurrencyItem.symbol
							productHrefElement.appendChild(productPriceElement)
						}

						if (currentProduct.isPurchasable) {
							var productAddWrapElement = document.createElement('a')
							productAddWrapElement.className = 'product_short_basket product_short_button product_buttonsmall_button button'
							productAddWrapElement.href = currentProduct.URL
							productContentElement.appendChild(productAddWrapElement)

							var productAddElement = document.createElement('span')
							productAddElement.className = 'button_text'
							productAddElement.innerHTML = currentProduct.addtobasket
							productAddWrapElement.appendChild(productAddElement)
						}

						new ProductShortComponent(discountProductElement)
					}
				}
			}
		}
	};
	var submitForm = function(event) {
		eventsManager.preventDefaultAction(event)
		if (conditionsCheckboxInput) {
			if (conditionsCheckboxInput.checked) {
				formElement.submit();
			} else {
				var message = []
				message['title'] = translationsLogics.get('shoppingbasket.conditions')
				message['content'] = '<a target="ART" class="modal_link" href="' + window.conditionsLink + '">' + translationsLogics.get('shoppingbasket.conditions_error') + '</a>'
				message['footer'] = translationsLogics.get('shoppingbasket.agreewithconditions')

				new ModalActionComponent(conditionsCheckboxInput, false, submitButtonElement, message); // checkbox-input, footer advanced, element for position, messages
			}
		}else {
			formElement.submit();
		}
	};
	init()
};

window.ShoppingBasketTotalsComponent = function(componentElement) {
	var deliveryRow, vatlessRow, vatRow, totalRow, productsFullPrice, pricesIncludeVatRow
	var discountRows = {}, serviceRows = {}

	var init = function() {
		productsFullPrice = createRow('productsfullprice', translationsLogics.get('shoppingbasket.productstable_productsprice'))
		deliveryRow = createRow('delivery', translationsLogics.get('shoppingbasket.deliveryprice'))
		if (shoppingBasketLogics.displayVat) {
			vatlessRow = createRow('vatless', translationsLogics.get('shoppingbasket.vatlesstotalprice'))
			vatRow = createRow('vat', translationsLogics.get('shoppingbasket.vatamount'))
			totalRow = createRow('total', translationsLogics.get('shoppingbasket.totalprice'))
		} else {
			totalRow = createRow('total', translationsLogics.get('shoppingbasket.totalprice'))
			pricesIncludeVatRow = createRow('pricesincludevat', translationsLogics.get('shoppingbasket.pricesincludevat'))
		}
	}
	var createRow = function(typeName, title) {
		var row = new ShoppingBasketTotalsRowComponent(typeName)
		row.setTitle(title)
		componentElement.appendChild(row.getComponentElement())
		return row
	}
	var createDiscountRow = function(discountInfo) {
		var row = new ShoppingBasketTotalsRowComponent('discount')
		row.setTitle(discountInfo.title)
		row.setPrice(-discountInfo.amount)
		if (shoppingBasketLogics.displayVat) {
			componentElement.insertBefore(row.getComponentElement(), vatlessRow.getComponentElement())
		} else {
			componentElement.insertBefore(row.getComponentElement(), totalRow.getComponentElement())
		}

		discountRows[discountInfo.code] = row
		return row
	}
	var createServiceRow = function(serviceInfo) {
		var row = new ShoppingBasketTotalsRowComponent('service')
		row.setTitle(serviceInfo.title)
		row.setPrice(serviceInfo.price)
		componentElement.insertBefore(row.getComponentElement(), deliveryRow.getComponentElement())

		serviceRows[serviceInfo.id] = row
		return row
	}
	this.updateData = function() {
		productsFullPrice.setPrice(shoppingBasketLogics.productsPrice)
		deliveryRow.setPrice(shoppingBasketLogics.deliveryPrice)
		var deliveryTitle = translationsLogics.get('shoppingbasket.deliveryprice')
		var selectedDelivery = shoppingBasketLogics.getSelectedDeliveryType()
		if (selectedDelivery) {
			deliveryTitle += ' (' + selectedDelivery.title + ')'
		}
		deliveryRow.setTitle(deliveryTitle)
		if (shoppingBasketLogics.displayVat) {
			vatlessRow.setPrice(shoppingBasketLogics.vatLessTotalPrice)
			vatRow.setPrice(shoppingBasketLogics.vatAmount)
		}

		totalRow.setPrice(shoppingBasketLogics.totalPrice)

		var discountsList = shoppingBasketLogics.getDiscountsList()
		var usedDiscountCodesMap = {}
		for (var i = 0; i < discountsList.length; ++i) {
			var discountInfo = discountsList[i]
			if (typeof discountRows[discountInfo.code] == 'undefined') {
				createDiscountRow(discountInfo)
			} else {
				discountRows[discountInfo.code].setTitle(discountInfo.title)
				discountRows[discountInfo.code].setPrice(-discountInfo.amount)
			}
			usedDiscountCodesMap[discountInfo.code] = true
		}
		for (var code in discountRows) {
			if (typeof usedDiscountCodesMap[code] == 'undefined') {
				componentElement.removeChild(discountRows[code].getComponentElement())
				delete (discountRows[code])
			}
		}

		var services = shoppingBasketLogics.getSelectedServices()
		var usedServiceIdsMap = {}
		for (var j = 0; j < services.length; ++j) {
			var serviceInfo = services[j]
			if (typeof serviceRows[serviceInfo.id] == 'undefined') {
				createServiceRow(serviceInfo)
			}
			usedServiceIdsMap[serviceInfo.id] = true
		}
		for (var id in serviceRows) {
			if (typeof usedServiceIdsMap[id] == 'undefined') {
				componentElement.removeChild(serviceRows[id].getComponentElement())
				delete (serviceRows[id])
			}
		}
	}
	this.getComponentElement = function() {
		return componentElement
	}
	init()
}

window.ShoppingBasketTotalsRowComponent = function(typeName) {
	var self = this
	var componentElement, titleElement, valueElement

	var init = function() {
		componentElement = self.makeElement('tr', 'shoppingbasket_total shoppingbasket_total_' + typeName)
		if (typeName == 'pricesincludevat') {
			titleElement = self.makeElement('th', 'shoppingbasket_total_title', componentElement)
			titleElement.colSpan = 6
		} else {
			titleElement = self.makeElement('th', 'shoppingbasket_total_title', componentElement)
			titleElement.colSpan = 4
			valueElement = self.makeElement('td', 'shoppingbasket_total_value', componentElement)
			valueElement.colSpan = 2
		}
	}
	this.setTitle = function(newTitle) {
		if (typeName == 'pricesincludevat') {
			titleElement.innerHTML = newTitle
		} else {
			titleElement.innerHTML = newTitle + ':'
		}

	}
	this.setPrice = function(newPrice) {
		if (newPrice !== '') {
			valueElement.innerHTML = domHelper.roundNumber(newPrice, 2) + ' ' + window.selectedCurrencyItem.symbol
			componentElement.style.display = ''
		} else {
			componentElement.style.display = 'none'
		}
	}
	this.getComponentElement = function() {
		return componentElement
	}
	init()
}
DomElementMakerMixin.call(ShoppingBasketTotalsRowComponent.prototype)

window.ShoppingBasketSelectionTable = function(componentElement) {

	var rowsContainerElement = false
	var productRowsList = []
	var productRowsIndex = {}

	var init = function() {
		rowsContainerElement = _('.shoppingbasket_table_rows', componentElement)[0]

		controller.addListener('startApplication', updateData)
		controller.addListener('shoppingBasketUpdated', updateData)
	}
	var updateData = function() {
		var products = window.shoppingBasketLogics.productsList
		var usedIdIndex = {}
		for (var i = 0; i < products.length; i++) {
			var basketProductId = products[i].basketProductId
			usedIdIndex[basketProductId] = true

			var product = false
			if (!productRowsIndex[basketProductId]) {
				product = new ShoppingBasketSelectionProduct(products[i])
				productRowsIndex[basketProductId] = product
				productRowsList.push(product)
				rowsContainerElement.appendChild(product.componentElement)
			} else {
				product = productRowsIndex[basketProductId]
			}

			if (product) {
				product.updateContents()
			}
		}
		for (var j = 0; j < productRowsList.length; j++) {
			var basketProductId2 = productRowsList[j].basketProductId
			if (typeof usedIdIndex[basketProductId2] == 'undefined') {
				rowsContainerElement.removeChild(productRowsList[j].componentElement)
				delete productRowsIndex[basketProductId2]
				productRowsList.splice(j, 1)
			}
		}
	}
	init()
}
window.ShoppingBasketSelectionProduct = function(initData) {
	var self = this

	var productData = false
	var changeTimeOut = false
	var keyUpDelay = 400
	var amountUpDelay = 200
	var minimumOrder = 1

	var imageElement
	var titleElement
	var codeElement
	var descriptionElement
	var priceElement
	var fullPriceElement
	var totalPriceElement
	var amountPlusButton
	var amountMinusButton
	var amountInput
	var removeButton

	this.componentElement = false
	this.basketProductId = false

	var init = function() {
		productData = initData
		self.basketProductId = productData.basketProductId
		minimumOrder = productData.minimumOrder
		createDomStructure()
		self.updateContents()
	}
	var createDomStructure = function() {
		self.componentElement = self.makeElement('tr', 'shoppingbasket_table_item')

		// image cell
		var cellElement = self.makeElement('td', 'shoppingbasket_table_image_container', self.componentElement)
		imageElement = cellElement.appendChild(self.makeElement('img', 'shoppingbasket_table_image', cellElement))
		// info cell
		cellElement = self.makeElement('td', 'shoppingbasket_table_title', self.componentElement)
		titleElement = self.makeElement('a', 'shoppingbasket_table_title', cellElement)
		codeElement = self.makeElement('div', 'shoppingbasket_table_code', cellElement)
		descriptionElement = self.makeElement('div', 'shoppingbasket_table_description', cellElement)

		// price cell
		cellElement = self.makeElement('td', 'shoppingbasket_table_price', self.componentElement)

		fullPriceElement = self.makeElement('div', 'shoppingbasket_table_full_price_value', cellElement)
		fullPriceElement.style.display = 'none'
		if (productData.salesPrice != productData.price) {
			domHelper.addClass(fullPriceElement, 'lined_price')
			fullPriceElement.style.display = 'block'
		}

		priceElement = self.makeElement('div', 'shoppingbasket_table_price_value', cellElement)

		// amount cell
		cellElement = self.makeElement('td', 'shoppingbasket_table_amount', self.componentElement)
		var amountContainerElement = self.makeElement('div', 'shoppingbasket_table_amount_container', cellElement)

		amountMinusButton = self.makeElement('a', 'button shoppingbasket_table_amount_minus', amountContainerElement)
		amountMinusButton.innerHTML = '<span class="button_text">-</span>'
		eventsManager.addHandler(amountMinusButton, 'click', minusClickHandler)

		amountInput = self.makeElement('input', 'input_component shoppingbasket_table_amount_input', amountContainerElement)
		eventsManager.addHandler(amountInput, 'keyup', amountKeyUpHandler)
		eventsManager.addHandler(amountInput, 'change', amountChangeHandler)
		new window.InputComponent({'componentClass': 'shoppingbasket_table_amount_block', 'inputElement': amountInput})

		amountPlusButton = self.makeElement('a', 'button shoppingbasket_table_amount_plus', amountContainerElement)
		amountPlusButton.innerHTML = '<span class="button_text">+</span>'
		eventsManager.addHandler(amountPlusButton, 'click', plusClickHandler)

		// total cell
		cellElement = self.makeElement('td', 'shoppingbasket_table_totalprice', self.componentElement)
		totalPriceElement = self.makeElement('span', 'shoppingbasket_table_totalprice_value', cellElement)

		// remove cell
		cellElement = self.makeElement('td', 'shoppingbasket_table_remove', self.componentElement)
		removeButton = self.makeElement('a', 'shoppingbasket_table_remove_button', cellElement)
		eventsManager.addHandler(removeButton, 'click', removeClickHandler)
	}
	var plusClickHandler = function(event) {
		eventsManager.preventDefaultAction(event)
		var amount = parseInt(amountInput.value, 10)
		amount = amount + minimumOrder
		amountInput.value = amount

		window.clearTimeout(changeTimeOut)
		changeTimeOut = window.setTimeout(changeAmount, amountUpDelay)
	}
	var minusClickHandler = function(event) {
		eventsManager.preventDefaultAction(event)
		var amount = parseInt(amountInput.value, 10)
		amount = amount - minimumOrder

		if (amount < minimumOrder) {
			amount = minimumOrder
		}

		amountInput.value = amount

		window.clearTimeout(changeTimeOut)
		changeTimeOut = window.setTimeout(changeAmount, amountUpDelay)
	}
	var amountKeyUpHandler = function() {
		window.clearTimeout(changeTimeOut)
		changeTimeOut = window.setTimeout(changeAmount, keyUpDelay)
	}
	var changeAmount = function() {
		var amount = parseInt(amountInput.value, 10)
		if (amount % minimumOrder != 0) {
			amount = minimumOrder
		}
		if (!isNaN(amount) && amount > 0) {
			registerEventHandlers()
			window.shoppingBasketLogics.changeAmount(self.basketProductId, amount)
		}
	}

	var shoppingBasketProductAdditionHandler = function() {
		unRegisterEventHandlers()
	}
	var shoppingBasketProductAddFailureHandler = function() {
		unRegisterEventHandlers()
		alert(window.translationsLogics.get('product.quantityunavailable'))
		amountInput.value--
	}

	var registerEventHandlers = function() {
		controller.addListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler)
		controller.addListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler)
	}

	var unRegisterEventHandlers = function() {
		controller.removeListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler)
		controller.removeListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler)
	}

	var amountChangeHandler = function() {
		window.clearTimeout(changeTimeOut)

		var amount = parseInt(amountInput.value, 10)
		if (isNaN(amount) || amount < 1) {
			amount = 1
		}
		if (amountInput.value != amount) {
			amountInput.value = amount
		}
		changeAmount()
	}

	var removeClickHandler = function(event) {
		eventsManager.preventDefaultAction(event)
		window.shoppingBasketLogics.removeProduct(self.basketProductId)
	}

	this.updateContents = function() {
		if (productData.image != '') {
			imageElement.src = productData.image
			imageElement.style.display = 'block'
		} else {
			imageElement.style.display = 'none'
		}

		titleElement.innerHTML = productData.title
		titleElement.href = productData.url
		codeElement.innerHTML = window.translationsLogics.get('shoppingbasket.productstable_productcode') + ': ' + productData.code
		var variations = []
		if (productData.variation) {
			if (typeof productData.variation == 'object' && productData.variation.length) {
				variations = productData.variation
			} else if (typeof productData.variation == 'string') {
				variations.push(productData.variation)
			}
		}
		if (variations.length) {
			descriptionElement.innerHTML = '<p>' + variations.join('</p><p>') + '</p>'
		} else {
			descriptionElement.innerHTML = ''
		}
		if (!productData.emptyPrice) {
			priceElement.innerHTML = domHelper.roundNumber(productData.salesPrice, 2) + ' ' + window.selectedCurrencyItem.symbol
			if (productData.unit) {
				priceElement.innerHTML += ' / ' + productData.unit
			}

			if (productData.salesPrice != productData.price) {
				fullPriceElement.innerHTML = domHelper.roundNumber(productData.price, 2) + ' ' + window.selectedCurrencyItem.symbol
				fullPriceElement.style.display = 'block'
			} else {
				fullPriceElement.style.display = 'none'
			}

			totalPriceElement.innerHTML = domHelper.roundNumber(productData.totalSalesPrice, 2) + ' ' + window.selectedCurrencyItem.symbol
		}
		amountInput.value = productData.amount
	}

	init()
}
DomElementMakerMixin.call(ShoppingBasketSelectionProduct.prototype)

window.ShoppingBasketSelectionServices = function(componentElement) {
	var containerElement
	var init = function() {
		if (containerElement = componentElement.querySelector('.shoppingbasket_services_list')) {
			controller.addListener('startApplication', updateContents)
			controller.addListener('shoppingBasketUpdated', updateContents)
		}
	}
	var updateContents = function() {
		var servicesList = shoppingBasketLogics.getServicesList()
		if (servicesList.length) {
			while (containerElement.firstChild) {
				containerElement.removeChild(containerElement.firstChild)
			}
			for (var i = 0; i < servicesList.length; i++) {
				var serviceComponent = new ShoppingBasketSelectionService(servicesList[i])
				containerElement.appendChild(serviceComponent.componentElement)
			}
			domHelper.removeClass(componentElement, 'shoppingbasket_services_component_hidden')
		} else {
			domHelper.addClass(componentElement, 'shoppingbasket_services_component_hidden')
		}
	}

	init()
}
window.ShoppingBasketSelectionService = function(info) {
	var self = this
	this.componentElement = null
	var inputElement

	var init = function() {
		var componentElement = document.createElement('label')
		componentElement.className = 'shoppingbasket_service_component'

		inputElement = document.createElement('input')
		inputElement.type = 'checkbox'
		inputElement.className = 'checkbox_placeholder'
		inputElement.value = '1'
		inputElement.checked = info.selected
		componentElement.appendChild(inputElement)
		window.checkBoxManager.createCheckBox(inputElement)
		eventsManager.addHandler(inputElement, 'change', changeHandler)

		var titleElement = document.createElement('span')
		titleElement.className = 'shoppingbasket_service_title'
		titleElement.colSpan = 4
		titleElement.innerHTML = info.title
		componentElement.appendChild(titleElement)

		var priceElement = document.createElement('span')
		priceElement.className = 'shoppingbasket_service_price'
		priceElement.colSpan = 2
		priceElement.innerHTML = domHelper.roundNumber(info.price, 2) + ' ' + window.selectedCurrencyItem.symbol
		componentElement.appendChild(priceElement)

		self.componentElement = componentElement
	}
	var changeHandler = function() {
		shoppingBasketLogics.setServiceSelection(info.id, inputElement.checked)
	}

	init()
}
window.ShoppingBasketPromoCodeComponent = function(componentElement) {
	var formElement
	var codeInputElement
	var submitButton
	var statusElement
	var titleElement
	var resetButton
	var init = function() {
		if (formElement = componentElement.querySelector('.shoppingbasket_promocode_form')) {
			if (codeInputElement = formElement.querySelector('.shoppingbasket_promocode_input')) {
				if (submitButton = formElement.querySelector('.shoppingbasket_promocode_button')) {
					submitButton.addEventListener('click', submitClickHandler)
				}
			}
		}
		if (statusElement = componentElement.querySelector('.shoppingbasket_promocode_status')) {
			titleElement = statusElement.querySelector('.shoppingbasket_promocode_status_title')

			if (resetButton = statusElement.querySelector('.shoppingbasket_promocode_status_reset')) {
				resetButton.addEventListener('click', resetClickHandler)

			}
		}
		controller.addListener('shoppingBasketUpdated', updateData)
		updateData()
	}
	var submitClickHandler = function() {
		var promoCode = codeInputElement.value
		if (promoCode) {
			shoppingBasketLogics.setPromoCode(promoCode)
		}
	}
	var resetClickHandler = function() {
		shoppingBasketLogics.setPromoCode('')
	}
	var updateData = function() {
		var discount = shoppingBasketLogics.getPromoCodeDiscount()
		if (discount) {
			formElement.style.display = 'none'
			statusElement.style.display = 'block'
			titleElement.innerHTML = discount.title
		} else {
			formElement.style.display = ''
			statusElement.style.display = 'none'
			titleElement.innerHTML = ''
		}
	}
	init()
}
window.ShoppingBasketSelectionForm = function(componentElement, formElement) {
	var payerContainerDisplayed = false

	var payerCheckBoxElement
	var payerDataElement
	var deliveryDataElement
	var fieldsBaseName

	var init = function() {
		if (payerCheckBoxElement = _('.shoppingbasket_payer_data_controls input', componentElement)[0]) {
			if (payerDataElement = _('.shoppingbasket_payer_data', componentElement)[0]) {
				eventsManager.addHandler(payerCheckBoxElement, 'change', checkBoxChangeHandler)
				if (!payerCheckBoxElement.checked) {
					payerContainerDisplayed = false
				}
			}
		}
		var fieldsBaseInput = _('.shoppingbasket_delivery_form_fieldsname', componentElement)[0]
		if (fieldsBaseInput) {
			fieldsBaseName = fieldsBaseInput.value
		}
		var element = _('.shoppingbasket_delivery_form_country', componentElement)[0]
		if (element) {
			new ShoppingBasketSelectionCountriesSelector(element)
		}
		element = _('.shoppingbasket_delivery_form_deliverytype', componentElement)[0]
		if (element) {
			new ShoppingBasketDeliveriesSelector(element)
		}
		element = _('.shoppingbasket_delivery_form_cities', componentElement)[0]
		if (element) {
			new ShoppingBasketSelectionCitiesSelector(element)
		}
		deliveryDataElement = _('.shoppingbasket_delivery_form_data', componentElement)[0]
		controller.addListener('startApplication', updateContents)
		controller.addListener('shoppingBasketUpdated', updateContents)
	}
	var updateContents = function() {
		var deliveryType = shoppingBasketLogics.getSelectedDeliveryType()
		if (deliveryType.hasNeededReceiverFields) {
			payerCheckBoxElement.checked = true
			eventsManager.fireEvent(payerCheckBoxElement, 'change')
			_('.shoppingbasket_payer_data_controls')[0].style.display = ''
		} else {
			payerCheckBoxElement.checked = false
			eventsManager.fireEvent(payerCheckBoxElement, 'change')
			_('.shoppingbasket_payer_data_controls')[0].style.display = 'none'
		}
		if (deliveryType) {
			while (deliveryDataElement.firstChild) {
				deliveryDataElement.removeChild(deliveryDataElement.firstChild)
			}

			// add delivery fields, make sure post24/smartpost ones come first
			var dpdPointField, dpdRegionField, post24automatField, post24regionField, smartPostAutomatField,
				smartPostRegionField
			for (var i = 0; i < deliveryType.deliveryFormFields.length; i++) {
				var autocomplete = deliveryType.deliveryFormFields[i].autocomplete
				if (typeof window.dpdLogics !== 'undefined') {
					if (autocomplete === 'dpdRegion') {
						dpdRegionField = deliveryType.deliveryFormFields[i]
						if (deliveryType.deliveryFormFields[i].value) {
							dpdLogics.setCurrentRegion(deliveryType.deliveryFormFields[i].value)
						}
					} else if (autocomplete === 'dpdPoint') {
						dpdPointField = deliveryType.deliveryFormFields[i]
					}
				}
				if (typeof window.post24Logics !== 'undefined') {
					if (autocomplete === 'post24Region') {
						post24regionField = deliveryType.deliveryFormFields[i]
						if (deliveryType.deliveryFormFields[i].value) {
							post24Logics.setCurrentRegion(deliveryType.deliveryFormFields[i].value)
						}
					} else if (autocomplete === 'post24Automate') {
						post24automatField = deliveryType.deliveryFormFields[i]
					}
				}
				if (typeof window.smartPostLogics !== 'undefined') {
					if (autocomplete === 'smartPostRegion') {
						smartPostRegionField = deliveryType.deliveryFormFields[i]
						if (deliveryType.deliveryFormFields[i].value) {
							smartPostLogics.setCurrentRegion(deliveryType.deliveryFormFields[i].value)
						}
					} else if (autocomplete === 'smartPostAutomate') {
						smartPostAutomatField = deliveryType.deliveryFormFields[i]
					}
				}
			}
			var field
			if (dpdRegionField) {
				field = new ShoppingBasketSelectionFormDpdRegion(dpdRegionField, fieldsBaseName)
				deliveryDataElement.appendChild(field.componentElement)
				if (dpdPointField) {
					field = new ShoppingBasketSelectionFormDpdPoint(dpdPointField, fieldsBaseName)
					deliveryDataElement.appendChild(field.componentElement)
				}
			}
			if (smartPostRegionField) {
				field = new ShoppingBasketSelectionFormSmartPostRegion(smartPostRegionField, fieldsBaseName)
				deliveryDataElement.appendChild(field.componentElement)
				if (smartPostAutomatField) {
					field = new ShoppingBasketSelectionFormSmartPostAutomate(smartPostAutomatField, fieldsBaseName)
					deliveryDataElement.appendChild(field.componentElement)
				}
			}
			if (post24regionField) {
				field = new ShoppingBasketSelectionFormPost24Region(post24regionField, fieldsBaseName)
				deliveryDataElement.appendChild(field.componentElement)
				if (post24automatField) {
					field = new ShoppingBasketSelectionFormPost24Automate(post24automatField, fieldsBaseName)
					deliveryDataElement.appendChild(field.componentElement)
				}
			}
			for (var j = 0; j < deliveryType.deliveryFormFields.length; j++) {
				var autocomplete2 = deliveryType.deliveryFormFields[j].autocomplete
				if (autocomplete2 != 'dpdPoint' && autocomplete2 != 'dpdRegion' && autocomplete2 != 'post24Automate' && autocomplete2 != 'post24Region' && autocomplete2 != 'smartPostRegion' && autocomplete2 != 'smartPostAutomate') {
					field = new ShoppingBasketSelectionFormField(deliveryType.deliveryFormFields[j], fieldsBaseName, formElement)
					deliveryDataElement.appendChild(field.getComponentElement())
				}
			}
		}
		updatePayerData()
	}
	var updatePayerData = function() {
		if (payerDataElement) {
			if (payerCheckBoxElement.checked) {
				if (payerContainerDisplayed) {
					hidePayerData()
				}
			} else {
				if (!payerContainerDisplayed) {
					displayPayerData()
				}
			}
		}
	}
	var displayPayerData = function() {
		payerContainerDisplayed = true

		TweenLite.to(payerDataElement, 0.5, {'css': {'height': payerDataElement.scrollHeight, 'opacity': 1}})
	}
	var hidePayerData = function() {
		payerContainerDisplayed = false

		TweenLite.to(payerDataElement, 0.5, {'css': {'height': 0, 'opacity': 0}})
	}
	var checkBoxChangeHandler = function() {
		updatePayerData()
	}

	init()
}

window.ShoppingBasketSelectionFormField = function(info, fieldsBaseName, formElement) {
	var self = this

	var componentElement
	var labelElement
	var starElement
	var fieldElement
	var fieldComponent
	var textareaElement
	var formElement

	var init = function() {
		componentElement = document.createElement('tr')
		if (info.error != '0' && info.error) {
			componentElement.className = 'form_error'
		}
		labelElement = self.makeElement('td', 'form_label', componentElement)
		starElement = self.makeElement('td', 'form_star', componentElement)
		fieldElement = self.makeElement('td', 'form_field', componentElement)

		if (info.required) {
			starElement.innerHTML = '*'
		}
		labelElement.innerHTML = info.title + ':'
		if (info.fieldType == 'select') {
			var parameters = {}
			parameters.className = 'shoppingbasket_delivery_form_dropdown'
			parameters.optionsData = info.options
			parameters.name = fieldsBaseName + '[' + info.fieldName + ']'
			fieldComponent = dropDownManager.createDropDown(parameters)
			if (info.value) {
				fieldComponent.setValue(info.value)
			}
			fieldElement.appendChild(fieldComponent.componentElement)
		} else if (info.fieldType == 'textarea') {
			textareaElement = document.createElement('textarea')
			textareaElement.className = 'textarea_component'
			textareaElement.setAttribute('name', fieldsBaseName + '[' + info.fieldName + ']')
			if (info.value) {
				textareaElement.value = info.value
			}
			new TextareaComponent(textareaElement)
			fieldElement.appendChild(textareaElement)
			if (info.helpLinkUrl && info.helpLinkText) {
				var helper = new ShoppingBasketSelectionFormFieldHelperComponent(info.helpLinkUrl, info.helpLinkText)
				fieldElement.appendChild(helper.getComponentElement())
			}
		} else {
			fieldComponent = new InputComponent({
				'name': fieldsBaseName + '[' + info.fieldName + ']',
				'value': info.value
			})
			fieldElement.appendChild(fieldComponent.componentElement)
			if (info.autocomplete === 'vatNumber') {
				var checkButton = document.createElement('input')
				checkButton.type = 'button'
				checkButton.value = window.translationsLogics.get('shoppingbasket.checkvat')
				checkButton.className = 'button check_vat_button'
				fieldComponent.componentElement.className = 'input_component check_vat_number_input'
				fieldElement.appendChild(checkButton)
			}
			eventsManager.addHandler(fieldComponent.inputElement, 'keydown', checkKey)
			if (info.helpLinkUrl && info.helpLinkText) {
				var helper = new ShoppingBasketSelectionFormFieldHelperComponent(info.helpLinkUrl, info.helpLinkText)
				fieldElement.appendChild(helper.getComponentElement())
			}
		}
	}
	this.getComponentElement = function() {
		return componentElement
	}
	var checkKey = function(event) {
		if (event.keyCode == 13) {
			formElement.submit()
		}
	}
	init()
}
DomElementMakerMixin.call(ShoppingBasketSelectionFormField.prototype)

window.ShoppingBasketSelectionFormFieldHelperComponent = function(url, title) {
	var self = this
	var componentElement

	var init = function() {
		componentElement = self.makeElement('div', 'shoppingbasket_delivery_form_field_helper')
		var linkElement = self.makeElement('a', 'shoppingbasket_delivery_form_field_helper_link', componentElement)
		linkElement.target = '_blank'
		linkElement.href = url
		linkElement.innerHTML = title
	}
	this.getComponentElement = function() {
		return componentElement
	}
	init()
}
DomElementMakerMixin.call(ShoppingBasketSelectionFormFieldHelperComponent.prototype)

window.ShoppingBasketSelectionFormPost24Region = function(info, fieldsBaseName) {
	var self = this

	var componentElement
	var labelElement
	var starElement
	var fieldElement
	var selectElement

	this.componentElement = null

	var init = function() {
		componentElement = document.createElement('tr')
		if (info.error != '0' && info.error) {
			componentElement.className = 'form_error'
		}

		labelElement = document.createElement('td')
		labelElement.className = 'form_label'
		componentElement.appendChild(labelElement)

		starElement = document.createElement('td')
		starElement.className = 'form_star'
		componentElement.appendChild(starElement)

		fieldElement = document.createElement('td')
		fieldElement.className = 'form_field'
		componentElement.appendChild(fieldElement)

		if (info.required) {
			starElement.innerHTML = '*'
		}

		labelElement.innerHTML = info.title + ':'

		selectElement = document.createElement('select')
		selectElement.name = fieldsBaseName + '[' + info.fieldName + ']'
		var selectedCountry = shoppingBasketLogics.getSelectedCountry()
		if (selectedCountry) {
			var regionsList = window.post24Logics.getCountryRegionsList(selectedCountry.iso3166_1a2)
			for (var i = 0; i < regionsList.length; i++) {
				var post24Info = regionsList[i]
				var option = document.createElement('option')
				option.text = post24Info.getName()
				option.value = post24Info.getName()
				selectElement.options.add(option)
				if (info.value && option.value == info.value) {
					selectElement.selectedIndex = i
				}
			}
		}

		fieldElement.appendChild(selectElement)
		eventsManager.addHandler(selectElement, 'change', changeHandler)

		var dropdown = dropDownManager.getDropDown(selectElement)
		fieldElement.appendChild(dropdown.componentElement)

		self.componentElement = componentElement
	}
	var changeHandler = function() {
		post24Logics.setCurrentRegion(selectElement.value)
	}
	init()
}
window.ShoppingBasketSelectionFormPost24Automate = function(info, fieldsBaseName) {
	var self = this

	var componentElement
	var labelElement
	var starElement
	var fieldElement
	var selectElement
	var dropdown

	this.componentElement = null

	var init = function() {
		componentElement = document.createElement('tr')
		if (info.error != '0' && info.error) {
			componentElement.className = 'form_error'
		}

		labelElement = document.createElement('td')
		labelElement.className = 'form_label'
		componentElement.appendChild(labelElement)

		starElement = document.createElement('td')
		starElement.className = 'form_star'
		componentElement.appendChild(starElement)

		fieldElement = document.createElement('td')
		fieldElement.className = 'form_field'
		componentElement.appendChild(fieldElement)

		if (info.required) {
			starElement.innerHTML = '*'
		}

		labelElement.innerHTML = info.title + ':'

		selectElement = document.createElement('select')
		selectElement.name = fieldsBaseName + '[' + info.fieldName + ']'
		fieldElement.appendChild(selectElement)
		eventsManager.addHandler(selectElement, 'change', changeHandler)

		dropdown = dropDownManager.getDropDown(selectElement)
		fieldElement.appendChild(dropdown.componentElement)

		self.componentElement = componentElement

		fillSelect()
		if (info.value) {
			for (var i = selectElement.options.length; i--;) {
				if (selectElement.options[i].value == info.value) {
					selectElement.selectedIndex = i
					dropdown.update()
					break
				}
			}
		}
		controller.addListener('post24RegionSelected', post24RegionSelectedHandler)
	}
	var post24RegionSelectedHandler = function() {
		fillSelect()
	}
	var fillSelect = function() {
		var region = window.post24Logics.getCurrentRegion()
		if (region) {
			while (selectElement.options.length > 0) {
				selectElement.options.remove(selectElement.options[0])
			}

			var list = region.getAutomatesList()
			for (var i = 0; i < list.length; i++) {
				var post24Info = list[i]
				var option = document.createElement('option')
				option.text = post24Info.getFullTitle()
				option.value = post24Info.getFullTitle()
				selectElement.options.add(option)
			}
			dropdown.update()
		}
	}
	var changeHandler = function() {

	}
	init()
}
window.ShoppingBasketSelectionFormDpdRegion = function(info, fieldsBaseName) {
	var self = this

	var componentElement
	var labelElement
	var starElement
	var fieldElement
	var selectElement

	this.componentElement = null

	var init = function() {
		componentElement = document.createElement('tr')
		if (info.error != '0' && info.error) {
			componentElement.className = 'form_error'
		}

		labelElement = document.createElement('td')
		labelElement.className = 'form_label'
		componentElement.appendChild(labelElement)

		starElement = document.createElement('td')
		starElement.className = 'form_star'
		componentElement.appendChild(starElement)

		fieldElement = document.createElement('td')
		fieldElement.className = 'form_field'
		componentElement.appendChild(fieldElement)

		if (info.required) {
			starElement.innerHTML = '*'
		}

		labelElement.innerHTML = info.title + ':'

		selectElement = document.createElement('select')
		selectElement.name = fieldsBaseName + '[' + info.fieldName + ']'
		var selectedCountry = shoppingBasketLogics.getSelectedCountry()
		if (selectedCountry) {
			var regionsList = window.dpdLogics.getCountryRegionsList(selectedCountry.iso3166_1a2)
			for (var i = 0; i < regionsList.length; i++) {
				var dpdInfo = regionsList[i]
				var option = document.createElement('option')
				option.text = dpdInfo.getName()
				option.value = dpdInfo.getId()
				selectElement.options.add(option)
				if (info.value && option.value == info.value) {
					selectElement.selectedIndex = i
				}
			}
		}
		fieldElement.appendChild(selectElement)
		eventsManager.addHandler(selectElement, 'change', changeHandler)

		var dropdown = dropDownManager.getDropDown(selectElement)
		fieldElement.appendChild(dropdown.componentElement)

		self.componentElement = componentElement
	}
	var changeHandler = function() {
		dpdLogics.setCurrentRegion(selectElement.value)
	}
	init()
}
window.ShoppingBasketSelectionFormDpdPoint = function(info, fieldsBaseName) {
	var self = this

	var componentElement
	var labelElement
	var starElement
	var fieldElement
	var selectElement
	var dropdown

	this.componentElement = null

	var init = function() {
		componentElement = document.createElement('tr')
		if (info.error != '0' && info.error) {
			componentElement.className = 'form_error'
		}

		labelElement = document.createElement('td')
		labelElement.className = 'form_label'
		componentElement.appendChild(labelElement)

		starElement = document.createElement('td')
		starElement.className = 'form_star'
		componentElement.appendChild(starElement)

		fieldElement = document.createElement('td')
		fieldElement.className = 'form_field'
		componentElement.appendChild(fieldElement)

		if (info.required) {
			starElement.innerHTML = '*'
		}

		labelElement.innerHTML = info.title + ':'

		selectElement = document.createElement('select')
		selectElement.name = fieldsBaseName + '[' + info.fieldName + ']'
		fieldElement.appendChild(selectElement)
		eventsManager.addHandler(selectElement, 'change', changeHandler)

		dropdown = dropDownManager.getDropDown(selectElement)
		fieldElement.appendChild(dropdown.componentElement)

		self.componentElement = componentElement

		fillSelect()
		if (info.value) {
			for (var i = selectElement.options.length; i--;) {
				if (selectElement.options[i].value == info.value) {
					selectElement.selectedIndex = i
					dropdown.update()
					break
				}
			}
		}
		controller.addListener('dpdRegionSelected', dpdRegionSelectedHandler)
	}
	var dpdRegionSelectedHandler = function() {
		fillSelect()
	}
	var fillSelect = function() {
		var region = window.dpdLogics.getCurrentRegion()
		if (region) {
			while (selectElement.options.length > 0) {
				selectElement.options.remove(selectElement.options[0])
			}

			var list = region.getPointsList()
			for (var i = 0; i < list.length; i++) {
				var dpdInfo = list[i]
				var option = document.createElement('option')
				option.text = dpdInfo.getFullTitle()
				option.value = dpdInfo.getFullTitle()
				selectElement.options.add(option)
			}
			dropdown.update()
		}
	}
	var changeHandler = function() {

	}
	init()
}

window.ShoppingBasketSelectionFormSmartPostRegion = function(info, fieldsBaseName) {
	var self = this

	var componentElement
	var labelElement
	var starElement
	var fieldElement
	var selectElement

	this.componentElement = null

	var init = function() {
		componentElement = document.createElement('tr')
		if (info.error != '0' && info.error) {
			componentElement.className = 'form_error'
		}

		labelElement = document.createElement('td')
		labelElement.className = 'form_label'
		componentElement.appendChild(labelElement)

		starElement = document.createElement('td')
		starElement.className = 'form_star'
		componentElement.appendChild(starElement)

		fieldElement = document.createElement('td')
		fieldElement.className = 'form_field'
		componentElement.appendChild(fieldElement)

		if (info.required) {
			starElement.innerHTML = '*'
		}

		labelElement.innerHTML = info.title + ':'

		selectElement = document.createElement('select')
		selectElement.name = fieldsBaseName + '[' + info.fieldName + ']'

		var regionsList = window.smartPostLogics.getRegionsList()
		for (var i = 0; i < regionsList.length; i++) {
			var smartPostInfo = regionsList[i]
			var option = document.createElement('option')
			option.text = smartPostInfo.getName()
			option.value = smartPostInfo.getName()
			selectElement.options.add(option)
			if (info.value && option.value == info.value) {
				selectElement.selectedIndex = i
			}
		}
		fieldElement.appendChild(selectElement)
		eventsManager.addHandler(selectElement, 'change', changeHandler)

		var dropdown = dropDownManager.getDropDown(selectElement)
		fieldElement.appendChild(dropdown.componentElement)

		self.componentElement = componentElement
	}
	var changeHandler = function() {
		smartPostLogics.setCurrentRegion(selectElement.value)
	}
	init()
}
window.ShoppingBasketSelectionFormSmartPostAutomate = function(info, fieldsBaseName) {
	var self = this

	var componentElement
	var labelElement
	var starElement
	var fieldElement
	var selectElement
	var dropdown

	this.componentElement = null

	var init = function() {
		componentElement = document.createElement('tr')
		if (info.error != '0' && info.error) {
			componentElement.className = 'form_error'
		}

		labelElement = document.createElement('td')
		labelElement.className = 'form_label'
		componentElement.appendChild(labelElement)

		starElement = document.createElement('td')
		starElement.className = 'form_star'
		componentElement.appendChild(starElement)

		fieldElement = document.createElement('td')
		fieldElement.className = 'form_field'
		componentElement.appendChild(fieldElement)

		if (info.required) {
			starElement.innerHTML = '*'
		}

		labelElement.innerHTML = info.title + ':'

		selectElement = document.createElement('select')
		selectElement.name = fieldsBaseName + '[' + info.fieldName + ']'
		fieldElement.appendChild(selectElement)
		eventsManager.addHandler(selectElement, 'change', changeHandler)
		dropdown = dropDownManager.getDropDown(selectElement)
		fieldElement.appendChild(dropdown.componentElement)

		self.componentElement = componentElement

		fillSelect()

		if (info.value) {
			for (var i = selectElement.options.length; i--;) {
				if (selectElement.options[i].value == info.value) {
					selectElement.selectedIndex = i
					dropdown.update()
					break
				}
			}
		}

		controller.addListener('smartPostRegionSelected', smartPostRegionSelectedHandler)
	}
	var smartPostRegionSelectedHandler = function() {
		fillSelect()
	}
	var fillSelect = function() {
		var region = window.smartPostLogics.getCurrentRegion()
		if (region) {
			while (selectElement.options.length > 0) {
				selectElement.options.remove(selectElement.options[0])
			}

			var list = region.getAutomatesList()
			for (var i = 0; i < list.length; i++) {
				var smartPostInfo = list[i]
				var option = document.createElement('option')
				option.text = smartPostInfo.getFullTitle()
				option.value = smartPostInfo.getFullTitle()
				selectElement.options.add(option)
			}
			dropdown.update()
		}
	}
	var changeHandler = function() {

	}
	init()
}
window.ShoppingBasketSelectionCountriesSelector = function(componentElement) {
	var countryDropDown
	var countriesList
	var fieldCell
	var singleOptionElement

	var self = this

	var init = function() {
		if (fieldCell = _('.form_field', componentElement)[0]) {
			var parameters = {}
			parameters.changeCallback = countryDropDownChange
			parameters.className = 'shoppingbasket_delivery_country_selector'
			parameters.optionsData = []
			countryDropDown = window.dropDownManager.createDropDown(parameters)
			fieldCell.appendChild(countryDropDown.componentElement)
			singleOptionElement = self.makeElement('span', 'shoppingbasket_delivery_form_sole_option', fieldCell)

			controller.addListener('startApplication', updateData)
			controller.addListener('shoppingBasketUpdated', updateData)
		}
	}
	var countryDropDownChange = function() {
		window.shoppingBasketLogics.selectDeliveryCountry(countryDropDown.value)
	}
	var updateData = function() {
		countriesList = window.shoppingBasketLogics.countriesList
		var optionsData = []
		for (var i = 0; i < countriesList.length; i++) {
			var countryData = countriesList[i]
			if (countryData.id == window.shoppingBasketLogics.selectedCountryId) {
				optionsData.push({value: countryData.id, text: countryData.title, selected: true})
			} else {
				optionsData.push({value: countryData.id, text: countryData.title, selected: false})
			}
		}
		countryDropDown.updateOptionsData(optionsData)

		if (countriesList.length > 0) {
			componentElement.style.display = ''
			if (countriesList.length == 1) {
				countryDropDown.componentElement.style.display = 'none'
				singleOptionElement.innerHTML = countriesList[0].title
				singleOptionElement.style.display = ''
			} else {
				countryDropDown.componentElement.style.display = ''
				singleOptionElement.style.display = 'none'
			}
		} else {
			componentElement.style.display = 'none'
		}
	}

	init()
}
DomElementMakerMixin.call(ShoppingBasketSelectionCountriesSelector.prototype)

window.ShoppingBasketDeliveriesSelector = function(componentElement) {
	var deliveryDropDown
	var deliveriesList
	var fieldCell
	var singleOptionElement

	var self = this

	var init = function() {
		if (fieldCell = _('.form_field', componentElement)[0]) {
			var parameters = {}
			parameters.changeCallback = deliveryDropDownChange
			parameters.className = 'shoppingbasket_delivery_selector'
			parameters.optionsData = []
			deliveryDropDown = window.dropDownManager.createDropDown(parameters)
			fieldCell.appendChild(deliveryDropDown.componentElement)
			singleOptionElement = self.makeElement('span', 'shoppingbasket_delivery_form_sole_option', fieldCell)
			controller.addListener('startApplication', updateData)
			controller.addListener('shoppingBasketUpdated', updateData)
		}
	}
	var deliveryDropDownChange = function() {
		tracking.checkoutOptionsTracking(2, deliveryDropDown.text)
		window.shoppingBasketLogics.selectDelivery(deliveryDropDown.value)
	}
	var updateData = function() {
		deliveriesList = window.shoppingBasketLogics.deliveryTypesList
		var optionsData = []
		for (var i = 0; i < deliveriesList.length; ++i) {
			var deliveryData = deliveriesList[i]
			if (deliveryData.id == window.shoppingBasketLogics.selectedDeliveryTypeId) {
				optionsData.push({value: deliveryData.id, text: deliveryData.title, selected: true})
			} else {
				optionsData.push({value: deliveryData.id, text: deliveryData.title, selected: false})
			}
		}
		deliveryDropDown.updateOptionsData(optionsData)

		if (deliveriesList.length > 0) {
			componentElement.style.display = ''
			if (deliveriesList.length == 1) {
				deliveryDropDown.componentElement.style.display = 'none'
				singleOptionElement.innerHTML = deliveriesList[0].title
				singleOptionElement.style.display = ''
			} else {
				deliveryDropDown.componentElement.style.display = ''
				singleOptionElement.style.display = 'none'
			}
		} else {
			componentElement.style.display = 'none'
		}
	}

	init()
}
DomElementMakerMixin.call(ShoppingBasketDeliveriesSelector.prototype)

window.ShoppingBasketSelectionCitiesSelector = function(componentElement) {
	var cityDropDown
	var citiesList
	var fieldCell
	var singleOptionElement

	var self = this

	var init = function() {
		if (fieldCell = _('.form_field', componentElement)[0]) {
			var parameters = {}
			parameters.changeCallback = cityDropDownChange
			parameters.className = 'shoppingbasket_city_selector'
			parameters.optionsData = []
			cityDropDown = window.dropDownManager.createDropDown(parameters)
			fieldCell.appendChild(cityDropDown.componentElement)
			singleOptionElement = self.makeElement('span', 'shoppingbasket_delivery_form_sole_option', fieldCell)

			controller.addListener('startApplication', updateData)
			controller.addListener('shoppingBasketUpdated', updateData)
		}
	}
	var cityDropDownChange = function() {
		window.shoppingBasketLogics.selectDeliveryCity(cityDropDown.value)
	}
	var updateData = function() {
		citiesList = window.shoppingBasketLogics.getCitiesList()
		var optionsData = []
		for (var i = 0; i < citiesList.length; ++i) {
			var cityData = citiesList[i]
			if (cityData.id == window.shoppingBasketLogics.selectedCityId) {
				optionsData.push({value: cityData.id, text: cityData.title, selected: true})
			} else {
				optionsData.push({value: cityData.id, text: cityData.title, selected: false})
			}
		}
		cityDropDown.updateOptionsData(optionsData)

		if (citiesList.length > 0) {
			componentElement.style.display = ''
			if (citiesList.length == 1) {
				cityDropDown.componentElement.style.display = 'none'
				singleOptionElement.innerHTML = citiesList[0].title
				singleOptionElement.style.display = ''
			} else {
				cityDropDown.componentElement.style.display = ''
				singleOptionElement.style.display = 'none'
			}
		} else {
			componentElement.style.display = 'none'
		}
	}

	init()
}
DomElementMakerMixin.call(ShoppingBasketSelectionCitiesSelector.prototype)