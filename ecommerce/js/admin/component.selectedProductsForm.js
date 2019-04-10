window.SelectedProductsFormComponent = function(componentElement) {
	var typeSelectElement;
	var autoSelectionRelatedElements, manualSelectionRelatedElements;

	var init = function() {
		if (typeSelectElement = _('select.selectedproducts_type_select', componentElement)[0]) {
			window.eventsManager.addHandler(typeSelectElement, 'change', selectChange);
			autoSelectionRelatedElements = _('.auto_selection_related');
			manualSelectionRelatedElements = _('.manual_selection_related');
			manipulateStructure(typeSelectElement.selectedIndex);
		}

		var element;
		if (element = _('.selectedproducts_connectedproducts', componentElement)[0]) {
			new AjaxSelectComponent(element, 'product', 'admin');
		}
		if (element = _('.selectedproducts_categoryselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'category', 'admin');
		}
		if (element = _('.selectedproducts_brandselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'brand', 'admin');
		}
		if (element = _('.selectedproducts_discountselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'discount', 'admin');
		}
		if (element = _('.selectedproducts_iconselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'genericIcon', 'admin');
		}
		if (element = _('.selectedproducts_form_parameters', componentElement)[0]) {
			new AjaxSelectComponent(element, 'productSelection', 'admin');
		}
	};

	var selectChange = function() {
		manipulateStructure(typeSelectElement.selectedIndex);
	};

	var manipulateStructure = function(type) {
		var automaticRelatedStyle = type ? "none" : "";
		var manualRelatedStyle = type ? "" : "none";

		for (var i = autoSelectionRelatedElements.length; i--;) {
			autoSelectionRelatedElements[i].style.display = automaticRelatedStyle;
		}
		for (var i = manualSelectionRelatedElements.length; i--;) {
			manualSelectionRelatedElements[i].style.display = manualRelatedStyle;
		}
	};
	init();
	controller.addListener('initDom', init);
};