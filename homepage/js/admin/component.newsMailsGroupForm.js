window.NewsMailsGroupForm = function(componentElement) {
	var addressSelectElement;

	var init = function() {
		if (addressSelectElement = _('select.newsmailsgroup_address_select', componentElement)[0]) {
			new AjaxSelectComponent(addressSelectElement, 'newsMailAddress', 'admin');
		}
	};
	init();
};