window.ProductFormComponent = function(componentElement) {
	var connectionsSelectElements;
	var categoryConnectionsSelectElements;
	var availabilitySelectElement;
	var quantityElement;
	var searchInputEl, resultIdEl, resultEl, resultContainerEl, resultRemoverEl;

	var init = function() {
		connectionsSelectElements = _('.connectedproducts_select', componentElement);
		for (var i = connectionsSelectElements.length; i--;) {
			new AjaxSelectComponent(connectionsSelectElements[i], 'product', 'admin');
		}

		categoryConnectionsSelectElements = _('.connectedcategories_select', componentElement);
		for (var i = categoryConnectionsSelectElements.length; i--;) {
			new AjaxSelectComponent(categoryConnectionsSelectElements[i], 'category', 'admin');
		}

		if (availabilitySelectElement = _('select.availability_select', componentElement)[0]) {
			quantityElement = _('.product_form_quantity', componentElement)[0];
			checkQuantityVisibility();
			eventsManager.addHandler(availabilitySelectElement, "change", checkQuantityVisibility)
		}
	};

	var checkQuantityVisibility = function() {
		var value = availabilitySelectElement.options[availabilitySelectElement.selectedIndex].value;
		if (value != "quantity_dependent") {
			quantityElement.style.display = "none";
		} else {
			quantityElement.style.display = "";
		}

	};
	init();
};