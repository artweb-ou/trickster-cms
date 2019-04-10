window.ProductSelectionFormComponent = function(componentElement) {
	var init = function() {
		var element;
		if (element = _('.product_selection_form_filterable_categories_select', componentElement)[0]) {
			new AjaxSelectComponent(element, 'category,productCatalogue', 'admin');
		}
	};
	init();
};