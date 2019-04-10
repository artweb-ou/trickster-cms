window.ProductImportFormComponent = function(componentElement) {
	var self = this;
	var searchInputEl;

	var init = function() {
		searchInputEl = _(".productimport_form_category_search", componentElement)[0];
		new AjaxSelectComponent(searchInputEl, "category", "admin");
	};
	init();
};