window.ProductSearchComponent = function(componentElement) {
	var sortSelectElement;
	var searchBaseUrl;
	var filters = [];
	var self = this;

	var init = function() {
		var elements = _('select.products_filter_dropdown_dropdown', componentElement);
		for (var i = elements.length; i--;) {
			filters[filters.length] = new ProductsDropdownFilterComponent(elements[i], self.refresh);
		}
		var elements = _('.products_filter_checkboxes', componentElement);
		for (var i = elements.length; i--;) {
			filters[filters.length] = new ProductsCheckboxesFilterComponent(elements[i], self.refresh);
		}
		var elements = _('.products_filter_price', componentElement);
		for (var i = elements.length; i--;) {
			filters[filters.length] = new ProductsFilterPriceComponent(elements[i], self.refresh);
		}
		sortSelectElement = _('select.productsearch_sortselect', componentElement)[0];
		if (sortSelectElement) {
			eventsManager.addHandler(sortSelectElement, 'change', sortChange);
		}
		var resetElement = _('.productsearch_reset', componentElement)[0];
		if (resetElement) {
			eventsManager.addHandler(resetElement, 'click', reset);
		}
		controller.addListener('TabsComponent.tabActivated', tabActivated);
	};

	var tabActivated = function() {
		for (var i = filters.length; i--;) {
			if (filters[i] instanceof ProductsFilterPriceComponent) {
				filters[i].refresh();
			}
		}
	};

	var sortChange = function(event) {
		self.refresh();
	};

	var reset = function() {
		// document.location.href = window.productsListElementUrl + 'productsearch:1/';
		document.location.href = window.productsListElementUrl;
	};

	this.refresh = function(changedFilter) {
		var arguments = {};
		var baseUrl = '';

		for (var i = filters.length; i--;) {
			if (filters[i].getType() != 'category') {
				filters[i].modifyFilterArguments(arguments);
			}
		}
		if (changedFilter && changedFilter.getType() == 'category') {
			var value = changedFilter.getValue();
			//if category was selected, we should move into selected category
			if (value && window.categoriesUrls[value]) {
				baseUrl = window.categoriesUrls[value];
			} else {
				//otherwise category was set to "None", so we should move into product catalogue itself
				baseUrl = window.productSearchCatalogueUrl;
			}
		}

		if (!baseUrl) {
			for (var i = filters.length; i--;) {
				if (filters[i].getType() == 'category') {
					var value = filters[i].getValue();
					if (value && window.categoriesUrls[value]) {
						baseUrl = window.categoriesUrls[value];
					}
				}
			}
		}

		if (sortSelectElement) {
			if (sortSelectElement.value) {
				arguments['sort'] = sortSelectElement.value;
			}
		} else {
			// workaround for retaining order
			var currentUrl = document.location.href;
			var sortArgumentPosition = currentUrl.indexOf('sort:');
			if (sortArgumentPosition > 0) {
				var sortArgument = currentUrl.slice(sortArgumentPosition + 5);
				if (sortArgument.indexOf('/') > 0) {
					sortArgument = sortArgument.slice(0, sortArgument.indexOf('/'));
				}
				if (sortArgument) {
					arguments['sort'] = sortArgument;
				}
			}
		}
		baseUrl = baseUrl || window.productsListElementUrl;
		document.location.href = baseUrl + generateQueryString(arguments);
		// document.location.href = baseUrl + generateQueryString(arguments) + 'productsearch:1/';
	};

	var generateQueryString = function(arguments) {
		var queryString = '';
		for (var key in arguments) {
			if (typeof arguments[key] != 'string') {
				queryString += key + ':' + arguments[key].join(',') + '/';
			} else {
				queryString += key + ':' + arguments[key] + '/';
			}
		}
		return encodeURI(queryString);
	};

	var onButtonClick = function(event) {
		eventsManager.preventDefaultAction(event);
		document.location.href = searchBaseUrl + generateQueryString();
	};

	var getSelectElementValue = function(element) {
		if (element && element.options[element.selectedIndex].value) {
			return element.options[element.selectedIndex].value;
		}
		return '';
	};
	init();
};