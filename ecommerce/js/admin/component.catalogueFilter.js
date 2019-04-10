window.CatalogueFilterComponent = function(componentElement) {
	var init = function() {
		var element;
		if (element = _('select.catalogue_filter_categoryselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'category', 'admin');
		}
		if (element = _('select.catalogue_filter_brandselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'brand', 'admin');
		}
		if (element = _('select.catalogue_filter_discountselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'discount', 'admin');
		}
	};
	init();
};